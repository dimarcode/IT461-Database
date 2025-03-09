# IT461-Database

Adapted from [The Flask Mega-Tutorial by Miguel Grinberg](https://blog.miguelgrinberg.com/post/the-flask-mega-tutorial-part-i-hello-world)

Database project for IT461 - Systems Analysis and Design

## Database migration
- First enter virtual environment (example uses docker):
```
docker exec -it <container-name> sh
```
- if you there is no "migrations" folder it means you haven't initialized the database, please do so
```
flask db init
```
- stage migration changes and add a descriptive note:
```
flask db migrate -m "<context for migration>"
```
- push changes
```
flask db upgrade
```
### to rollback changes for whatever reason:
```
flask db downgrade
```

For more migration details: [LINK](https://blog.miguelgrinberg.com/post/the-flask-mega-tutorial-part-iv-database)