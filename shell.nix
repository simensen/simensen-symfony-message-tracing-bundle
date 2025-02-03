{ pkgs ? import <nixpkgs> {}}:

let
  configuredPkgs = {
    php = pkgs.php.withExtensions ({ all, enabled }: enabled ++ (with all; [ gnupg ]));
  };
in
  pkgs.mkShell {
    name = "simensen-framer";
    packages = [
      configuredPkgs.php
      configuredPkgs.php.packages.composer
      configuredPkgs.php.packages.phive
      pkgs.gnupg
      pkgs.yamllint
    ];
    shellHook =
      ''
        export PATH=$(pwd)/tools:$PATH
      '';
  }
