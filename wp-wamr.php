<?php
/*
* Plugin Name: WAMR for Wordpress
* Description: WebAssembly Micro Runtime for Wordpress
* Version: 0.1
* Author: AsmNext
* Author URI: https://asmnext.com
*/

define( 'WP_WAMR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

ini_set('max_execution_time', '0');

function wp_wamr_exec($atts = array(), $content = null, $tag = '') {
    $result = "";

    $_atts = shortcode_atts(
        array(
            'filename' => 'test.wasm',
            'function' => '',
            'stacksize' => 0,
            'heapsize' => 0,
            'repl' => 'false',
            'env' => '',  // query string (e.g. 'key1=value1&key2=value2') 
            'dir' => '',   // comma sperated (e.g. '/mnt/wasi1,/mmt/wasi2')
            'args' => ''
        ), $atts, $tag
    );

    // Path of 'iwasm' runtime
    // Source code: https://github.com/bytecodealliance/wasm-micro-runtime
    $wamr_bin_path = WP_WAMR_PLUGIN_DIR . 'bin/iwasm';

    // Build a command line
    $cmd = array();

    if (file_exists($wamr_bin_path)) {
        array_push($cmd, $wamr_bin_path);

        if($_atts['stacksize'] > 0) {
            array_push($cmd, '--stack-size=' . $_atts['stacksize']);
        }

        if($_atts['heapsize'] > 0) {
            array_push($cmd, '--heap-size=' . $_atts['heapsize']);
        }

        if($_atts['repl'] == 'true') {
            array_push($cmd, '--repl');
        }

        if(!empty($_atts['env'])) {
            $_envs = explode('&', $_atts['env']);
            foreach($_envs as $_env) {
                array_push($cmd, '--env="' . addslashes($_env) . '"');
            }
        }

        if(!empty($_atts['dir'])) {
            $_dirs = explode(',', $_atts['dir']);
            foreach($_dirs as $_dir) {
                array_push($cmd, '--dir="' . addslashes($_dir) . '"');
            }
        }

        if (!empty($_atts['function'])) {
            array_push($cmd, '--function');
            array_push($cmd, $_atts['function']);   
        }

        // Path of WASM binary
        array_push($cmd, WP_WAMR_PLUGIN_DIR . 'wasm-bin/' . $_atts['filename']);

        // Add arguments 
        if (!empty($_atts['args'])) {
            array_push($cmd, $_atts['args']);
        }

        // Get stdout
        $result = shell_exec(implode(' ', $cmd));
    } else {
        $result = "[Error] WAMR not found!";
    }

    return $result;
}

function wp_wamr_benchmark() {
    $result = "";

    include_once(WP_WAMR_PLUGIN_DIR . 'benchmark/tower_of_hanoi.php');

    // Tower of Hanoi - 4 disks
    $result .= "<br>Tower of Hanoi - 4 disks";
    $ms = microtime(true);
    hanoi_move(4, 'A', 'B', 'C');
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (PHP/Native)";
    $ms = microtime(true);
    shell_exec('php ' . WP_WAMR_PLUGIN_DIR . 'benchmark/tower_of_hanoi_4disks.php');
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (PHP/Shell)";
    $ms = microtime(true);
    wp_wamr_exec(array('filename' => 'tower_of_hanoi_4disks.wasm'));
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (WASM/Shell)";
    $result .= "<br>";

    // Tower of Hanoi - 8 disks
    $result .= "<br>Tower of Hanoi - 8 disks";
    $ms = microtime(true);
    hanoi_move(8, 'A', 'B', 'C');
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (PHP/Native)";
    $ms = microtime(true);
    shell_exec('php ' . WP_WAMR_PLUGIN_DIR . 'benchmark/tower_of_hanoi_8disks.php');
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (PHP/Shell)";
    $ms = microtime(true);
    wp_wamr_exec(array('filename' => 'tower_of_hanoi_8disks.wasm'));
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (WASM/Shell)";
    $result .= "<br>";

    // Tower of Hanoi - 16 disks
    $result .= "<br>Tower of Hanoi - 16 disks";
    $ms = microtime(true);
    hanoi_move(16, 'A', 'B', 'C');
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (PHP/Native)";
    $ms = microtime(true);
    shell_exec('php ' . WP_WAMR_PLUGIN_DIR . 'benchmark/tower_of_hanoi_16disks.php');
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (PHP/Shell)";
    $ms = microtime(true);
    wp_wamr_exec(array('filename' => 'tower_of_hanoi_16disks.wasm'));
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (WASM/Shell)";
    $result .= "<br>";

    // Tower of Hanoi - 20 disks
    $result .= "<br>Tower of Hanoi - 20 disks";
    $ms = microtime(true);
    hanoi_move(20, 'A', 'B', 'C');
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (PHP/Native)";
    $ms = microtime(true);
    shell_exec('php ' . WP_WAMR_PLUGIN_DIR . 'benchmark/tower_of_hanoi_20disks.php');
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (PHP/Shell)";
    $ms = microtime(true);
    wp_wamr_exec(array('filename' => 'tower_of_hanoi_20disks.wasm'));
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (WASM/Shell)";
    $result .= "<br>";

    // Tower of Hanoi - 24 disks
    $result .= "<br>Tower of Hanoi - 24 disks";
    $ms = microtime(true);
    hanoi_move(24, 'A', 'B', 'C');
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (PHP/Native)";
    $ms = microtime(true);
    shell_exec('php ' . WP_WAMR_PLUGIN_DIR . 'benchmark/tower_of_hanoi_24disks.php');
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (PHP/Shell)";
    $ms = microtime(true);
    wp_wamr_exec(array('filename' => 'tower_of_hanoi_24disks.wasm'));
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (WAMR/Shell)";
    $result .= "<br>";

    // Tower of Hanoi - 28 disks
    $result .= "<br>Tower of Hanoi - 28 disks";
    $ms = microtime(true);
    hanoi_move(28, 'A', 'B', 'C');
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (PHP/Native)";
    $ms = microtime(true);
    shell_exec('php ' . WP_WAMR_PLUGIN_DIR . 'benchmark/tower_of_hanoi_28disks.php');
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (PHP/Shell)";
    $ms = microtime(true);
    wp_wamr_exec(array('filename' => 'tower_of_hanoi_28disks.wasm'));
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (WASM/Shell)";
    $result .= "<br>";

    return $result;
}

add_shortcode('wamr_exec', 'wp_wamr_exec');
add_shortcode('wamr_benchmark', 'wp_wamr_benchmark');

