# activity tracking
Uploadable activity files to be parsed into a user friendly dashboard / csv export

## What activity files?

https://www.workingon.co allows exports of csv files and they are in the following format.

```
,Date,Timezone,Task,Author,Team,Source,Author Timezone
,2019-04-04 05:32 PM,Europe/London,#home,Andy Franklin,The Drum,,Europe/London
```

## Why?

https://www.workingon.co seems to be a fairly nice tool to track what you're currently working on. However,
it lacks some basic functionality that would be useful to our purposes.

These features include
* Duration spent on an activity
* Linking an activity to a project
* Having a pause and resume option (as there is no timer on workingon this is understandable)
* A nice overview of the teams work

## The goal

The end goal for this project is to be able to take the CSV export from https://www.workingon.co/activity.csv and
upload it here at the end of the day or week. This will then be able to be exported in a customisable spreadsheet.

It would be nice to have a Project entity with an allocated time allowance. This Project should be linked to a Tag
and the time per project per Author should be viewable. This should also be able to be broken down by secondary 
(Or more) Tags.

The user should be able to sepcify their own Pause and Resume tags. These will be seeded with pause being #lunch, 
#pause, #tea, #break. And Resume being #resume, #continue

## Dev setup

Install dependencies
```bash
composer install
```

Specify your database settings in .env file at the root of the project

Force create the database
```bash
php bin/console doc:sch:up --force
```

Run the web server (If someone wants to dockerise this please do so!)
```bash
php bin/console ser:run
```

