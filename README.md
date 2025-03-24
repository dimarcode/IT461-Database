
# IT461-Database

### Database project for IT461 - Systems Analysis and Design
---

Ports:

| Destination  | Port  |
| ------------ | ----- |
| Website      | 8981  |
| MySQL server | 33061 |
| phpmyadmin   | 8982  |

>[!important] Double check that these do not conflict with anything you are already running

#### Directions:
---

- To set up locally:
	- Proceed directly to step 2 if you are not running in Docker
1. If running in **Docker**:
	- In the same folder as the original, make a copy of `.env.dist`
		- Rename that copy to just `.env`
		- Set variables in that file according to your own setup
2. Do not skip this step:
	- In the same folder as the original, make a copy of `connect.php.dist`
		- Rename that copy to just `connect.php`
		- Change variables according to your own setup
3. If you already had a database set up, make sure your schema matches the one in `/mysql/import-csv.sh`
	- If you were running MySql in a container, remove the container and erase the volume, then run `docker compose down` and `docker compose up -d`
		- The database will automatically be rebuilt to match the one in `import-csv.sh`

>[!note] Please note: this version of the project is built to run in a containerized environment, with containers for MySQL server, phpmyadmin, and apache to serve the files locally. Running it this way allows for automating the database construction, and pulling in the data provided by the professor. If you are not using docker, you should be able to just use the files in the `/php/www` folder.  Because this folder files is configured as a mount point, the files here will persist even when the containers are stopped or removed, and you can edit them directly. Doing so will reflect changes on the website.

