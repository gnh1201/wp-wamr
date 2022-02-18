<?php
/*
* Plugin Name: WAMR for Wordpress
* Description: WebAssembly Micro Runtime (WAMR) for Wordpress
* Version: 0.1
* Author: AsmNext Team
* Author URI: https://asmnext.com
*/

define( 'WP_WAMR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

function wp_wamr_exec($atts = array(), $content = null, $tag = '') {
    $result = "";

    $_atts = shortcode_atts(
        array(
            'filename' => 'test',
            'function' => '',
            'stacksize' => 0,
            'heapsize' => 0,
            'repl' => 'false',
            'env' => '',  // query string (e.g. 'key1=value1&key2=value2') 
            'dir' => '',   // comma sperated (e.g. '/mnt/wasi1,/mmt/wasi2')
            'args' => '',
            'benchmark' => 'false'
        ), $atts, $tag
    );

    // Path of 'iwasm' runtime
    // Source code: https://github.com/bytecodealliance/wasm-micro-runtime
    $wamr_bin_path = WP_WAMR_PLUGIN_DIR . 'bin/iwasm';

    // Build a command line
    $cmd = array();
    $is_tmpfile = false;

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

        if(!empty($_atts['function'])) {
            array_push($cmd, '--function');
            array_push($cmd, $_atts['function']);   
        }

        // Path of WASM binary
        $filepath = wp_media_load_wasm($_atts['filename']);
        if (!empty($filepath)) {
            $is_tmpfile = true;
        } else {
            $filepath = WP_WAMR_PLUGIN_DIR . 'wasm-bin/' . $_atts['filename'] . '.wasm';
        }
        array_push($cmd, $filepath);

        // Add arguments 
        if(!empty($_atts['args'])) {
            array_push($cmd, $_atts['args']);
        }

        // Build a command line
        $_cmd = implode(' ', $cmd);

        // Get stdout
        if($_atts['benchmark'] == 'true') {
            $ms = microtime(true);
            shell_exec($_cmd);
            $_ms = microtime(true);
            $result .= "[Benchmark] " . sprintf('%.8fs', ($_ms - $ms)) . " (WASM/Shell)";
        } else {
            $result .= shell_exec($_cmd);
        }
    } else {
        $result .= "[Error] WAMR not found!";
    }

    // Remove WASM file 
    if($is_tmpfile) {
        @unlink($filepath);
        @rmdir(substr($filepath, 0, strripos($filepath, '/')));
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
    shell_exec('php ' . WP_WAMR_PLUGIN_DIR . 'benchmark/tower_of_hanoi_4disks');
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (PHP/Shell)";
    $result .= "<br>" . wp_wamr_exec(array('filename' => 'tower_of_hanoi_4disks.wasm', 'benchmark' => 'true'));
    $result .= "<br>";

    // Tower of Hanoi - 8 disks
    $result .= "<br>Tower of Hanoi - 8 disks";
    $ms = microtime(true);
    hanoi_move(8, 'A', 'B', 'C');
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (PHP/Native)";
    $ms = microtime(true);
    shell_exec('php ' . WP_WAMR_PLUGIN_DIR . 'benchmark/tower_of_hanoi_8disks');
    $_ms = microtime(true);
    $result .= "<br>" . sprintf('%.8fs', ($_ms - $ms)) . " (PHP/Shell)";
    $result .= "<br>" . wp_wamr_exec(array('filename' => 'tower_of_hanoi_8disks', 'benchmark' => 'true'));
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
    $result .= "<br>" . wp_wamr_exec(array('filename' => 'tower_of_hanoi_16disks', 'benchmark' => 'true'));
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
    $result .= "<br>" . wp_wamr_exec(array('filename' => 'tower_of_hanoi_20disks', 'benchmark' => 'true'));
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
    $result .= "<br>" . wp_wamr_exec(array('filename' => 'tower_of_hanoi_24disks', 'benchmark' => 'true'));
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
    $result .= "<br>" . wp_wamr_exec(array('filename' => 'tower_of_hanoi_28disks', 'benchmark' => 'true'));
    $result .= "<br>";

    return $result;
}

function wp_media_load_wasm($filename) {
    global $wpdb;

    $old_filepath = "";
    $filepath = "";

    $site_url = site_url();
    $docroot = realpath($_SERVER["DOCUMENT_ROOT"]);  // ends with slash (/)

    $sys_tmpdir = sys_get_temp_dir();
    $tmpdir = $sys_tmpdir . '/' . substr(md5(mt_rand()), 0, 7);

    if(mkdir($tmpdir)) {
        $results = $wpdb->get_results( "select guid from {$wpdb->prefix}posts where post_type = 'attachment' and post_title = '{$filename}.wasm' order by post_date desc limit 1 ", OBJECT );
        foreach($results as $attachment) {
            if ($site_url == substr($attachment->guid, 0, strlen($site_url))) {
                $old_filepath = $docroot . substr($attachment->guid, strlen($site_url));
            }
        }

        // For security reason, the media file must be compressed like '.wasm.zip'
        if (!empty($old_filepath) && file_exists($old_filepath)) {
            $zip = new ZipArchive();
            $result = $zip->open($old_filepath);
            if ($result === TRUE) {
                $zip->extractTo($tmpdir . '/');
                $zip->close();

                $_filepath = $tmpdir . '/' . $filename . '.wasm';
                if(file_exists($_filepath)) {
                    $filepath = $_filepath;
                } else {
                    echo "[Error] Failed to extract ZIP file";
                }
            } else {
                echo "[Error] Invaild ZIP file";
            }
        } else {
            echo "[Error] No exists WASM (.wasm.zip) file in Media Library";
        }
    }

    return $filepath;
}

add_shortcode('wamr_exec', 'wp_wamr_exec');
add_shortcode('wamr_benchmark', 'wp_wamr_benchmark');
