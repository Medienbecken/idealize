# IdeaLizee

For now we use docker compose to get the environment needed (Wordpress Core and Database).
The customized Files as well as the content of the Database are stored in this repo and mounted into the containers.

## Usage
After running "docker compose up"

- **idealize** can be found at *localhost:8000*
- **ILIAS** can be found at *localhost:8888* The login information is randomly generated and printed out in the console
- **phpMyAdmin** is available at *localhost:8080*

## Sources

With the docker-compose file getting more and mor complex it might be a good idea to keep track about what we use, where it's from and where some documentation can be found.

1. MySQL: We use the offical Docker container from dockerhub.
2. Wordpress: We use the official Docker container from dockerhub.
3. phpMyAdmin: We use the container from phpmyadmin/phpmadmin.
4. Ilias: We use the container from sturai/ilias. More about it can be found here: [link:https://hub.docker.com/r/sturai/ilias]


## Issues

1. The ilias installation is kind of slow (~2min.) so I suggest to run "docker compose up" not in detached mode. That way, you can see in your console when ILIAS is running.
3. Adding a proxy to the list of services would allow us to have pathes like localhost/idealize, localhost/ilias... intead of a specific port for each service.