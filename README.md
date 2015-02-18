# SemaMinifierBundle
This Symfony2 bundle allows you to minify html code, combine and minify JavaScript files and CSS files without Java VM.

Installation
============

## Install using composer.json
If you are using composer to manage your project, just add the following
line to your composer.json file

    {
        "require": {
        	"sema/minifier-bundle": "dev-master"
        }
    }

Then update the vendor libraries:

```shell
composer.phar update
# OR
composer.phar update sema/minifier-bundle # to only update the bundle
```


## Register the bundle

You must register the bundle in your kernel:

    <?php

    // app/AppKernel.php
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Sema\Bundle\MinifierBundle\SemaMinifierBundle(),
        );
        // ...
    }

-----------------------

#Use
===

You have two options for minify html:
1. onKernelResponse listener
2. Twig Extension

## Enable minify html using onKernelResponse listener
You need add follow lines in config.yml
```yml
    #app/config.yml
    sema_minifier:
        enable_listener: true
```
All responses will be minified

## Enable minify html using Twig Extension
You need modify base template (base.html.twig or layout.html.twig or something else), add {% minifyhtml %} to the top of the file and {% endminifyhtml %} to the bottom of the file.
You need get something like this
```twig
    #base.html.twig
    {% minifyhtml %}
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8" />
            <title>{% block title %}Welcome!{% endblock %}</title>
            {% block stylesheets %}{% endblock %}
            <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
        </head>
        <body>
            {% block body %}{% endblock %}
            {% block javascripts %}{% endblock %}
        </body>
    </html>
    {% endminifyhtml %}
```
##Use combine and minify js or css files
The configuration file is a JSON file that describes all the rules for combining static files. Each group is assigned a file name to which we refer in the templates. This allows you to not get attached to the physical location of the file, which can be handy when you change the file structure. Below is a sample configuration file assets.json
```json
{
    "css": {
        "main": {
            "output": "assets/css/dist/main.min.css",
            "input": [
                "bundles/framework/css/structure.css",
                "bundles/framework/css/body.css"
            ]
        }
    },
    "js": {
        "vendor": {
            "output": "assets/js/dist/vendor.min.js",
            "input": [
                "assets/libs/jquery/dist/jquery.js"
            ]
        },
        "html5shiv": {
            "output": "assets/js/dist/html5shiv.min.js",
            "input": "assets/libs/html5shiv/dist/html5shiv.js"
        },
        "form": {
            "output": "assets/js/dist/form.min.js",
            "input": [
                "assets/libs/parsleyjs/dist/parsley.js",
                "assets/libs/jquery-ui/jquery-ui.js",
                "assets/js/form.js"
            ]
        }
    }
}
```
Depending on the debug mode expansion will transmit a pattern or an array of source files, or an array of processed files. Here is an example template:
```twig
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>{% block title %}Welcome!{% endblock %}</title>
        {% for url in assets.css['main'] %}
            <link rel="stylesheet" href="{{ asset(url) }}" />
        {% endfor %}
        {% block stylesheets %}{% endblock %}
        <!--[if lt IE 9]>
            <script src="{{ asset(assets.js['html5shiv'][0], version='1') }}"></script>
        <![endif]-->
        {% for url in assets.js['vendor'] %}
            <script src="{{ asset(url) }}"></script>
        {% endfor %}
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
    </head>
    <body>
        {% block body %}{% endblock %}
        {% block javascripts %}{% endblock %}
    </body>
</html>
```