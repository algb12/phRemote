# phRemote

A remote which works wherever PHP works. All that's required is that your computer runs PHP at least on version 5.4.0 (for the integrated PHP web-server), with the JSON module installed and enabled.

## Running phRemote

To run phRemote, simply download this repository as a ZIP-file, and extract its contents into a directory. In a terminal, switch to this directory, and run `php -S 192.168.0.2:8000`, using the IP address and port of your choice.

phRemote will automatically generate a UI from the loaded modules, and allow you to control your computer using commands from modules (see below on how to write a module).

The client browser should support JSON and rudimentary AJAX capabilities, and have JavaScript enabled for phRemote to be functional. The integrated PHP web-server is used both, because it runs as the logged-in user, which solves many permission issues, and because it would be too cumbersome to set up a whole stack just for phRemote.

To access the phRemote UI, simply go to the IP address and port with which the server was started.

## Modules

phRemote is cross-platform by using "modules", which support, multiple platforms. Modules are JSON files following the schema below:

```json
{
    "metadata": {
        "module": "module_filename.json",
        "label": "Human friendly module name",
        "description": "Module description",
        "author": "E. X. Ample",
        "author_email": "author@example.org",
        "date_created": "2017-01-01"
    },
    "init": {
        "datafields": {
            "info_field_name1": {
                "Platform1": "command1",
                "Platform2": "command2"
            },
            "info_field_name2": {
                "Platform1": "command1",
                "Platform2": "command2"
            }
        }
    },
    "updaters": {
        "updater1": {
            "Platform1": "command1",
            "Platform2": "command2"
        },
        "updater2": {
            "Platform1": "command1",
            "Platform2": "command2"
        }
    },
    "commands": {
        "command_name1": {
            "label": "Command 1 button text",
            "cmd": {
                "Platform1": "command1",
                "Platform2": "command2"
            },
            "updaters": ["updater1, updater2"]
        },
        "command_name2": {
            "label": "Command 2 button text",
            "input": {
                "type": "range",
                "range_min": 0,
                "range_max": 100
            },
            "cmd": {
                "Platform1": "command1 {{value}}",
                "Platform2": "command2"
            },
            "updaters": ["updater1, updater2"]
        }
    },
    "info": {
        "info_field_name1": {
            "label": "Info field 1 label:"
        },
        "info_field_name2": {
            "label": "Info field 2 label:"
        }
    }
}
```

### Sections of a module

The `metadata` section contains all the relevant information about the module itself. The most important field is the `module` field, which MUST be equivalent to the filename of the module.

The `init` section contains the commands to be executed which retrieve the initial state of the different parameters. The so-called "datafields" are the areas of the UI where the data for the relevant parameters is displayed. Not all modules must contain an `init` section.

The `updaters` section contains the code to be executed to retrieve a callback to return back to the client browser. Commands are mapped to the relevant datafields.

The `commands` section MUST be present in every module. It contains the command to be executed for each OS type, and specifies the updaters which succeed the command. The `input` subsection specifies what type of input should be used for the command. See the list of available inputs and their config options below.

The `info` section defines the datafields displaying values for parameters.

### Inputs

A command can have an "input" defined as a subsection of the relevant command in the module file.

Currently available inputs are:

- `button`: A simple button, does not pass any data to the command. The default input when none is specified.
- `range`: A range slider, with following properties:

  - `range_min`: The minimum of the range slider (default is `0`)
  - `range_max`: The maximum of the range slider (default is `100`)
  - Defined as follows within a module:

    ```json
    "input": {
        "type": "range",
        "range_min": 0,
        "range_max": 100
    }
    ```

  - The command itself can use the `{{value}}` placeholder to access the value passed by the range slider.

- `text`: A text box, with an update button. Clicking on the update button will submit the text box content as a value to the command.

  - Defined as follows within a module:

    ```json
    "input": {
        "type": "text"
    }
    ```

  - The command itself can use the `{{value}}` placeholder to access the value passed by the text box.

### OS/Platform types

A module may contain commands which are executed depending on the OS phRemote runs on. The 4 OS types are:

- Windows: `WIN`
- OS X: `OSX`
- Linux: `LINUX`
- Unknown: `UNKNOWN`

### The modules.json file

`modules.json`, found in the `config` directory, contains an array of modules phRemote should load, as follows:

```json
{
    "modules": [
        "module1.json",
        "module2.json"
    ]
}
```

## Config

### Authentication

phRemote has basic authentication, which can be enabled or disabled. By default it is disabled, and the default username is **admin** and the password is **password**.

To enable authentication, edit the `config.php` file in the config directory.

## Contributions and feedback

If you have any questions and/or feedback, or are willing to create cross-platform modules for phRemote, please do not hesitate to contact me via my email: <algb12.19@gmail.com> or open up any issues you may face on GitHub!
