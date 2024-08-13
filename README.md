# wp-wamr
WebAssembly Micro Runtime (WAMR) for Wordpress - Motivated from [BusyBox](https://busybox.net/)

## Corver (Low-level Developer)
![Corver](src/images/wp-wamr_corver.jpg)

## Features
  * Integrate with Wordpress Media Library - Upload an `something-package-1.zip` file, and use the shortcode.
  
    ```
    [wamr_exec packagename="something-package-1" filename="tower_of_hanoi_16disks" benchmark="true"]
    ```

  * Check a file integrity

    The package file must have an `MD5SUM` or `SHA1SUM` file.

## Todo
  * Add support multi-binaries (Following the OS type, the Linux kernel version, the GLIBC version)

## Ultimate goals
  * Write a Wordpress plugins with C, C++, C#, Python, Go, Rust, and more languages

## Runtime source code and build (WAMR)
  * https://github.com/bytecodealliance/wasm-micro-runtime
  * https://gist.github.com/gnh1201/b9c4ee4a98fbb369237b3e447a9550a2

## LICENSE
  * gnh1201/wp-wamr -  BSD-2-Clause License (AsmNext)
  * bytecodealliance/wasm-micro-runtime - Apache 2.0 License (Bytecode Alliance)

