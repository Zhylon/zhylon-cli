# Zhylon CLI

Zhylon CLI is a command-line tool that you may use to manage your Zhylon resources from the command-line.

## Overview

Zhylon provides a command-line tool that you may use to manage your Zhylon servers, sites, and resources from the command-line.

## Installation

> Requires **[PHP 8.2+](https://php.net/releases/)**

You may install the **[Zhylon CLI](https://github.com/Zhylon/zhylon-cli)** as a global **[Composer](https://getcomposer.org/)** dependency:

```sh
composer global require zhylon/zhylon-cli
```

## Get Started

To view a list of all available Zhylon CLI commands and view the current version of your installation, you may run the zhylon command from the command-line:

```sh
zhylon
```

## Authenticating

You will need to generate an API token to interact with the Zhylon CLI. 
Tokens are used to authenticate your account without providing personal details. 
API tokens can be created from **[Zhylon’s API dashboard](https://zhylon.net/user/api-tokens)**.

After you have generated an API token, you should authenticate with your Zhylon account using the login command:

```sh
zhylon login
zhylon login --token=your-api-token
```

Alternatively, if you plan to authenticate with Zhylon from your CI platform, you may set a `ZHYLON_API_TOKEN` environment variable in your CI build environment.

## Current Server & Switching Servers

When managing Zhylon servers, sites, and resources via the CLI, you will need to be aware of your currently active server.
You may view your current server using the `server:current` command. 
Typically, most of the commands you execute using the Zhylon CLI will be executed against the active server.

```sh
zhylon server:current
```

Of course, you may switch your active server at any time.
To change your active server, use the `server:switch` command:

```sh
zhylon server:switch
zhylon server:switch staging
```

To view a list of all available servers, you may use the `server:list` command:

```sh
zhylon server:list
```

## SSH Key Authentication

Before performing any tasks using the Zhylon CLI, you should ensure that you have added an SSH key for the `zhylon` user to your servers so that you can securely connect to them.
You may have already done this via the Zhylon UI.
You may test that SSH is configured correctly by running the `ssh:test` command:

```sh
zhylon ssh:test
```

To configure SSH key authentication, you may use the `ssh:configure` command.
The `ssh:configure` command accepts a `--key` option which instructs the CLI which public key to add to the server. 
In addition, you may provide a `--name` option to specify the name that should be assigned to the key:

```sh
zhylon ssh:configure

zhylon ssh:configure --key=/path/to/public/key.pub --name=sallys-macbook
```

After you have configured SSH key authentication, you may use the `ssh` command to create a secure connection to your server:

```sh
zhylon ssh

zhylon ssh server-name
```

## Sites

To view the list of all available sites, you may use the `site:list` command:

```sh
zhylon site:list
```

### Initiating Deployments

One of the primary features of Zhylon is deployments. Deployments may be initiated via the Zhylon CLI using the `deploy` command:

```sh
zhylon deploy

zhylon deploy example.com
```

### Updating Environment Variables

You may update a site’s environment variables using the `env:pull` and `env:push` commands.
The `env:pull` command may be used to pull down an environment file for a given site:

```sh
zhylon env:pull
zhylon env:pull pestphp.com
zhylon env:pull pestphp.com .env
```

Once this command has been executed the site’s environment file will be placed in your current directory.
To update the site’s environment variables, simply open and edit this file.
When you are done editing the variables, use the `env:push` command to push the variables back to your site:

```sh
zhylon env:push
zhylon env:push pestphp.com
zhylon env:push pestphp.com .env
```

If your site is utilizing Laravel’s “configuration caching” feature or has queue workers, the new variables will not be utilized until the site is deployed again.

### Viewing Application Logs

You may also view a site’s logs directly from the command-line.
To do so, use the `site:logs` command:

```sh
zhylon site:logs
zhylon site:logs --follow              # View logs in realtime

zhylon site:logs example.com
zhylon site:logs example.com --follow  # View logs in realtime
```

### Reviewing Deployment Output / Logs

When a deployment fails, you may review the output / logs via the Zhylon UI’s deployment history screen.
You may also review the output at any time on the command-line using the `deploy:logs` command.
If the `deploy:logs` command is called with no additional arguments, the logs for the latest deployment will be displayed.
Or, you may pass the deployment ID to the `deploy:logs` command to display the logs for a particular deployment:

```sh
zhylon deploy:logs

zhylon deploy:logs h:9D8YNkVKXB7l1JEZa0
```

### Running Commands

Sometimes you may wish to run an arbitrary shell command against a site.
The `command` command will prompt you for the command you would like to run.
The command will be run relative to the site’s root directory.

```sh
zhylon command

zhylon command example.com

zhylon command example.com --command="php artisan inspire"
```

### Tinker

As you may know, all Laravel applications include “Tinker” by default.
To enter a Tinker environment on a remote server using the Zhylon CLI, run the `tinker` command:

```sh
zhylon tinker

zhylon tinker example.com
```

## Resources

Zhylon provisions servers with a variety of resources and additional software, such as Nginx, MySQL, etc.
You may use the Zhylon CLI to perform common actions on those resources.

### Checking Resource Status

To check the current status of a resource, you may use the `{resource}:status` command:

```sh
zhylon daemon:status
zhylon database:status

zhylon nginx:status

zhylon php:status      # View PHP status (default PHP version)
zhylon php:status 8.4  # View PHP 8.4 status
```

### Viewing Resources Logs

You may also view logs directly from the command-line.
To do so, use the `{resource}:logs` command:

```sh
zhylon daemon:logs
zhylon daemon:logs --follow  # View logs in realtime

zhylon database:logs

zhylon nginx:logs         # View error logs
zhylon nginx:logs access  # View access logs

zhylon php:logs           # View PHP logs (default PHP version)
zhylon php:logs 8.4       # View PHP 8.4 logs
```

### Restarting Resources

Resources may be restarted using the `{resource}:restart` command:

```sh
zhylon daemon:restart

zhylon database:restart

zhylon nginx:restart

zhylon php:restart      # Restarts PHP (default PHP version)
zhylon php:restart 8.4  # Restarts PHP 8.4
```

### Connecting To Resources Locally

You may use the `{resource}:shell` command to quickly access a command line shell that lets you interact with a given resource:

```sh
zhylon database:shell
zhylon database:shell my-database-name
zhylon database:shell my-database-name --user=my-user
```
