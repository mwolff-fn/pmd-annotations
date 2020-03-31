# Annotate pull-requests based on a PMD XML-report

Turns PMD style XML-reports into Github pull-request [annotations via the Checks API](https://developer.github.com/v3/checks/).
This script is meant for use within your Github Action.

That means you no longer have to search through your Github Action log files or the console output.
No need to interpret messages which are formatted differently with every tool.
Instead you can focus on your pull-request, and you don't need to leave the pull-request area.

![Logs Example](https://github.com/mheap/phpunit-github-actions-printer/blob/master/phpunit-printer-logs.png?raw=true)

![Context Example](https://github.com/mheap/phpunit-github-actions-printer/blob/master/phpunit-printer-context.png?raw=true)
_Images from https://github.com/mheap/phpunit-github-actions-printer_

# Installation

Install the binary via Composer
```bash
composer require mridang/pmd-annotations
```

# Example Usage

`pmd2pr` can be used on a already existing PMD-report XML-report. Alternatively you might use it in the UNIX pipe notation to chain it into your existing cli command.

Run one of the following commands within your Github Action workflow:

## Process a PMD formatted file

```bash
vendor/bin/pmd2pr /path/to/pmd-report.xml
```

### Available Options

- `--graceful-warnings`: Don't exit with error codes if there are only warnings
- `--colorize`: Colorize the output. Useful if the same lint script should be used locally on the command line and remote on Github Actions. With this option, errors and warnings are better distinguishable on the command line and the output is still compatible with Github annotations


## Pipe the output of another commmand

This works for __any__ command which produces a PMD-formatted report. Examples can bee seen below:

### Using [PHPMD](https://github.com/phpmd/phpmd)

```bash
phpmd . xml codesize,naming,unusedcode,controversial,design --exclude libs,var,build,tests --ignore-violations-on-exit | vendor/bin/pmd2pr
```

## Example GithubAction workflow

If you're using `shivammathur/setup-php` to setup PHP, `pmd2pr` binary is shipped within:

```yml
# ...
jobs:
    phpmd-analysis:
      name: phpmd static code analysis
      runs-on: ubuntu-latest
      steps:
          - uses: actions/checkout@v2
          - name: Setup PHP
            uses: shivammathur/setup-php@v1
            with:
                php-version: 7.3
                coverage: none # disable xdebug, pcov
                tools: pmd2pr
          - run: |
                composer install # install your apps dependencies
                vendor/bin/phpmd . xml codesize,naming,unusedcode,controversial,design --exclude libs,var,build,tests --ignore-violations-on-exit | pmd2pr
```

If you use a custom PHP installation, then your project needs to require `mridang/pmd-annotations`

```yml
# ...
jobs:
    phpmd-analysis:
      name: phpmd static code analysis
      runs-on: ubuntu-latest
      steps:
          - uses: actions/checkout@v2
          - name: Setup PHP
            run: # custom PHP installation
          - run: |
                composer install # install your apps dependencies
                composer require mridang/pmd-annotations # install pmd2pr
                vendor/bin/phpmd . xml codesize,naming,unusedcode,controversial,design --exclude libs,var,build,tests --ignore-violations-on-exit | vendor/bin/pmd2pr
```

# Resources

[GithubAction Problem Matchers](https://github.com/actions/toolkit/blob/master/docs/problem-matchers.md)
