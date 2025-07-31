{
  description = "PHP development environment for simensen-symfony-messenger-message-tracing";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";
    flake-utils.url = "github:numtide/flake-utils";
  };

  outputs =
    {
      self,
      nixpkgs,
      flake-utils,
    }:
    flake-utils.lib.eachDefaultSystem (
      system:
      let
        pkgs = nixpkgs.legacyPackages.${system};

        parle = pkgs.php.buildPecl {
          pname = "parle";
          version = "0.8.5";

          src = pkgs.fetchFromGitHub {
            owner = "weltling";
            repo = "parle";
            rev = "parle-0.8.5";
            sha256 = "sha256-Z7MEOESu4qhefIX3y5YjomZXmZutVqKXHTbRRwn37ZQ=";
          };

          meta = with pkgs.lib; {
            description = "Parsing and lexing";
            homepage = "https://github.com/weltling/parle";
            license = licenses.bsd2;
          };
        };

        configuredPkgs = {
          php = pkgs.php.withExtensions (
            { all, enabled }:
            enabled
            ++ (with all; [
              gnupg
              xdebug
            ])
            ++ [ parle ]
          );
        };
      in
      {
        devShells.default = pkgs.mkShell {
          name = "simensen-symfony-messenger-message-tracing";
          packages = [
            configuredPkgs.php
            configuredPkgs.php.packages.composer
            configuredPkgs.php.packages.phive
            pkgs.gnupg
            pkgs.yamllint
          ];
          shellHook = ''
            export PATH=$(pwd)/tools:$PATH
            export PHP_CS_FIXER_IGNORE_ENV=1
          '';
        };
      }
    );
}
