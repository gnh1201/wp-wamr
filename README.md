# wp-wamr
WebAssembly Micro Runtime (WAMR) for Wordpress

## Features
  * Integrate with Wordpress Media Library - Upload an `something.wasm.zip` file, and use the shortcode.

    ```
    [wamr_exec filename="tower_of_hanoi_16disks" benchmark="true"]
    ```

## Todo
  * Add support multi-binaries (Following the OS type, the Linux kernel version, the GLIBC version)

## Ultimate goals
  * Write an Wordpress plugins with C, C++, C#, Python, Go, and more languages

## Source code of WAMR
  * https://github.com/bytecodealliance/wasm-micro-runtime

## LICENSE
  * gnh1201/wp-wamr -  BSD-2-Clause License (AsmNext)
  * bytecodealliance/wasm-micro-runtime - Apache 2.0 License (Bytecode Alliance)
