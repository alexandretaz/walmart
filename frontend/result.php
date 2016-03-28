<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 28/03/16
 * Time: 01:13
 */

/**
 * Return the valids origins
 * @return array
 * @todo refactory body (Make it Dry code)
 */
function get_valid_origins()
{
    global $wpdb;

    $return = array();

    $walmart_section_table_name = $wpdb->prefix . "route_section";

    $sql = "Select origin from $walmart_section_table_name GROUP BY origin";

    $arrResults = $wpdb->get_results($sql, ARRAY_A);
    if (!empty($arrResults)) {
        foreach ($arrResults as $origin) {
            $return[] = $origin['origin'];
        }
    }
    return $return;
}

/**
 * @return array
 * @todo refactory body (Make it Dry code)
 */
function get_valid_destinations()
{
    global $wpdb;

    $return = array();

    $walmart_section_table_name = $wpdb->prefix . "route_section";

    $sql = "Select destination from $walmart_section_table_name GROUP BY destination";

    $arrResults = $wpdb->get_results($sql, ARRAY_A);
    if (!empty($arrResults)) {
        foreach ($arrResults as $destination) {
            $return[] = $destination['destination'];
        }
    }
    return $return;
}

/**
 * @return response
 */
function get_routes() {
    global $wpdb;
    $origins = get_valid_origins();
    $destinations = get_valid_destinations();
    $request = new request(request::GET, $origins, $destinations);
    $finalDestination = $request->getDestination();
    $beginOrigin = $request->getOrigin();
    $autonomy = (float) $request->getAutonomy();
    $price = number_format($request->getGasValue());
    

    $walmart_section_table_name = $wpdb->prefix .  "route_section";

    $possibleResponses = array();

    $endroutes = $wpdb->get_results(
        "Select origin, destination, distance from $walmart_section_table_name where destination = '$finalDestination'"
    );
    foreach($endroutes as $endroute) {
        if(strcasecmp($endroute->origin, $beginOrigin) === 0 ) {
            $response = new response();
            $response->setRoute("$beginOrigin $finalDestination");
            $cost = ($endroute->distance / $autonomy) * $price;
            $response->setCost($cost);
            $possibleResponses[] = $response;
        }
        else{
            $subroutes = get_section_route($finalDestination, $beginOrigin);
            if(!empty($subroutes)) {
                $possibleResponses = array_merge($possibleResponses, $subroutes );
            }
        }
    }
    $response = route_calculation(filter_possible_responses($possibleResponses, $finalDestination, $beginOrigin), $request);

    if(isset($response)) {
        return $response;
    }

}

function route_calculation(response $response, request $request) {
    $totalDistance = $response->getTotalDistance();
    $cost = number_format((($totalDistance/$request->getAutonomy())*$request->getGasValue()),2);
    $response->setCost($cost);
    return $response;
}

function filter_possible_responses($possiblesResponses, $destination, $origin) {
    $lesserDistance = null;
    $n = 1;
    $possiblesResponsesCopy = $possiblesResponses;
    foreach($possiblesResponsesCopy as $key=>$response) {
        $route = $response->getRoute();
        $lastPoint = $route[(strlen($route)-1)];
        $beginPoint = $route[0];
        if(
        empty($response->getRoute()) ||
        $response->getTotalDistance() === null ||
        strcasecmp($lastPoint, $destination) !== 0 ||
        strcasecmp($beginPoint, $origin) !== 0
        ) {
            unset($possiblesResponses[$key]);
            continue;
        }
        if($lesserDistance === null) {
            $n++;$keyToUse = $key;
            $lesserDistance = $response->getTotalDistance();
        }
        else{
            $keyToUse = ($response->getTotalDistance()<$lesserDistance) ? $key : $keyToUse;
            $lesserDistance = $response->getTotalDistance();
        }
        $n++;

    }
    return $possiblesResponses[$keyToUse];
}


function get_section_route($destination, $origin, $nesting = 0) {
    global $wpdb;

    $possibles = array();

    $walmart_section_table_name = $wpdb->prefix .  "route_section";
    ++$nesting;
    if($nesting == 10) {
        return;
    }
    $destinationRoutes = $wpdb->get_results(
        "Select origin, destination, distance from $walmart_section_table_name where destination = '$destination'"
    );

    foreach($destinationRoutes as $wayToFinal) {
        $response = new response();
        $originRoutes = $wpdb->get_results(
            "Select origin, destination, distance from $walmart_section_table_name where origin = '$origin' AND destination= '{$wayToFinal->origin}'", "ARRAY_A"
        );
        if(empty($originRoutes)) {
            $section_response = get_section_route($wayToFinal->origin, $origin, $nesting);
            if(empty($section_response)) {
                return;
            }
            foreach($section_response as $key=>$subResponse) {
                $subResponse->setRoute("{$subResponse->getRoute()} $destination");
                $subResponse->setTotalDistance($wayToFinal->distance);
                $section_response[$key] = $subResponse;
            }
            $possibles = array_merge($possibles, $section_response);
        }
        else{
            foreach($originRoutes as $originRoute) {
                $response->setRoute("{$origin} {$wayToFinal->origin} {$destination}");
                $response->setTotalDistance($originRoute['distance']+$wayToFinal->distance);
            }
        }
        $possibles[] = $response;
    }
    return $possibles;
}

function api_result( $query ) {
    $pagename = empty($query->query_vars['pagename']) ? "":$query->query_vars['pagename'];

    if(empty($pagename) || strcasecmp($pagename, 'api-routes')!==0) {
        return;
    }
    try {
        $response = get_routes();
    }
    catch (Exception $e) {
        $response = new stdClass();
        $response->status = "Error";
        $response->message = $e->getMessage();
    } 
    wp_send_json($response);
    die();
}