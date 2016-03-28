<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 28/03/16
 * Time: 00:23
 */
/**
 * Generate the routes menu
 */
function routes_menu() {
    add_menu_page("Routes Page", "Walmart Test","read", "routes-admin", "route_management");
}

/**
 * Generate the page to manage the routes on the database
 */
function route_management() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    if(!empty($_POST) && check_new_route()) {
        add_new_route();
    }
    elseif(!empty($_POST) && !check_new_route()){
        echo "<p><b> Route couldn't be inserted</b></p>";
    }
    $routes = get_routes_on_db();
    echo '<h2> Check the Routes on the Database</h2>';
    echo '<div class="wrap">';
    if(empty($routes)) {
        echo __('No routes on the db');
    }
    else {
        echo '<table id="routes_table"> <thead><tr><th>id</th>' . '<th>' . __('origin') . '</th><th>' . __('destination') . '</th>' . '<th>' . __('distance') . '</th></tr></thead>';

        foreach ($routes as $tableRow) {
            echo '<tr><td>'.$tableRow->id.'</td><td>'.$tableRow->origin.'</td><td>'.$tableRow->destination.'</td><td>'.$tableRow->distance.'</td></tr>';
        }
        echo '</table>';
    }
    echo get_form_to_add_route();
    echo '</div>';
}

function add_new_route() {
    global $wpdb;

    $walmart_section_table_name = $wpdb->prefix .  "route_section";

    $wpdb->insert(
        $walmart_section_table_name,
        array(
            'origin' => $_POST["origin"],
            'destination' => $_POST["destination"],
            'distance' => $_POST["distance"],
        )
    );
}

/**
 * @return bool
 */
function check_new_route() {
    if ( isset($_POST['origin'], $_POST['destination'], $_POST['distance'] )
        && (!empty($_POST['origin']) && !empty($_POST['destination']) && !empty($_POST['distance']))
        ) {
        return true;
    }
    return false;
}

/**
 * Get all Routes on the Db to Create a new route
 * @return array|null|object
 */
function get_routes_on_db () {
    global $wpdb;

    $walmart_section_table_name = $wpdb->prefix .  "route_section";

    $routes = $wpdb->get_results(
        "Select id, origin, destination, distance from $walmart_section_table_name order by id"
    );

    return $routes;

}

/**
 * Return the form to add a new Route
 * @return string
 */
function get_form_to_add_route() {
    $strFormNewRoute = "<div> 
        <h2> Add a new Route Section</h2>
        <form action='/wp-admin/plugins.php?page=routes-admin' method='post'>
            <label>Origin:
                <input type='text' name='origin' />
            </label>
            <label>Destination:
                <input type='text' name='destination' />
            </label>
            <label>Distance:
                <input type='number' name='distance' />
            </label>
            <input type='submit' name='submit'>
        </form>
    </div>";

    return $strFormNewRoute;
}