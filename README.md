#Breathe Code Talent Tree API

Before starting ot use the API methods you have to request for an access_token by autenticating your client app, with one of these following options:

###1. Using ClientCredentials to get access_token

**Request an access_token by doing the following request:**

    POST <client_id>:<client_secret> https://talenttree-alesanchezr.c9users.io/api/token
    PARAMS grant_type=client_credentials

**The response will be something like this:**

    {
        "access_token": "7ab8d4abaa369c76b447e5d10387650ff628f3dc",
        "expires_in": 86400,
        "token_type": "Bearer",
        "scope": null
    }

###2. Using UserCredentials to get access_token

**Request an access_token by doing the following request:**

    POST <client_id>:<client_secret> https://talenttree-alesanchezr.c9users.io/api/token
    PARAMS grant_type=password&username=<users_username>&password=<users_password>

**The response will be something like this:**

    {
        "access_token": "8d452bcb5b64cca657b6b28f6da5347c12f0fa39",
        "expires_in": 86400,
        "token_type": "Bearer",
        "scope": null,
        "refresh_token": "b52a5790f22846d2c6c3b5044f6ca88523724e88"
    }

After you have your "authorization code" you can use any API request by appending the authorization code as a GET or post parameter.

##RESOURCES

    1. Badges
        1.1 Get badges of student [GET]
        1.2 Get all badges [GET]
    2. Single Badge
        2.1 Get single badge [GET]
        2.2 Create or update single badge [POST]
        2.3 Delete single badge [DELETE]
    3. Student
        3.0 Get all students [GET]
        3.1 Get single Student [GET]
        3.2 Create one Student [POST]
        3.3 Delete single student [DELETE]
    4. Activity
        4.1 Get student latest activities [GET]
        4.2 Add activity to student [POST]
        4.3 Delete activity from student
    5. Specialty
        5.1 Get single specialty [GET]
        5.2 Create specialty [POST]
        5.3 Delete specialty [DELETE]
    

As you develop throughout the academy, you will earn "talent badges" that all together will become your "Talent Tree".

##Badges Collection [/badges/]

### Get all badges [GET]

+ Response 200 (application/json)

        {
            "code": 200,
            "data": {
                "name": "Master in CSS Selectors",
                "earned_at": "2014-11-11T08:40:51.620Z",
                "url": "/badge/1",
                "image_url": "/path/to/image",
                "points_to_achieve": 50,
                "technologies": [
                    "js", "swift"
                ]
            }
        }

##Student Badges Collection [/badges/student/{student_id}]

### Get student badges [GET]

+ Parameters
    + student_id (number, optional) - ID of the student

+ Response 200 (application/json)

        {
            "code": 200,
            "data": {
                "name": "Master in CSS Selectors",
                "earned_at": "2014-11-11T08:40:51.620Z",
                "url": "/badge/1",
                "image_url": "/path/to/image",
                "points_to_achieve": 50,
                "technologies": [
                    "js", "swift"
                ]
            }
        }
        
## Single Badge Collection [/badge/{?id}{?slug}]

### Get single badge [GET]

+ Parameters
    + slug (string, optional) - Slug of the badge
    + id (string, optional) - Id of the badge

+ Response 200 (application/json)

        {
            "code": 200,
            "data": {
                "id": 1,
                "slug": "css_master",
                "name": "Master in CSS Selectors",
                "earned_at": "2014-11-11T08:40:51.620Z",
                "url": "/badge/1",
                "image_url": "/path/to/image",
                "points_to_achieve": 50,
                "technologies": [
                    "js", "swift"
                ]
            }
        }
        
### Create or update single badge [POST]

You can update a badge or create it if no ID or SLUG is passed.

+ Request (application/json)
    + Body
    {
        "slug": "css_master",
        "name": "Master in CSS Selectors",
        "image_url": "/path/to/image",
        "description": "This badge is give to real css masters",
        "points_to_achieve": 50,
        "technologies": "js, swift"
    }

+ Response 201 (application/json)

        {
            "code": 200,
            "data": {
                "id": 1,
                "slug": "css_master",
                "name": "Master in CSS Selectors",
                "earned_at": "2014-11-11T08:40:51.620Z",
                "url": "/badge/1",
                "image_url": "/path/to/image",
                "points_to_achieve": 50,
                "technologies": [
                    "js", "swift"
                ]
            }
        }
        
### Delete single badge [DELETE]

A badge can only be deleted if it has no activity with 5 days old. Otherwise it will be marked as "archived".

+ Request (application/json)
    + Attributes
        +id            (string, optional) - The badge id
        +slug            (string, optional) - The badge slug

+ Response 201 (application/json)
        
        {
            "code": 200,
            "message": "ok"
        }
        
## Students Collection [/students/]

### Get all students [GET]

+ Response 200 (application/json)

        {
            "code": 200,
            "data": [
                {
                    "id": 1,
                    "breathecode_id": 1,
                    "email": "john@4geeks.co",
                    "avatar_url": "",
                    "full_name": "John",
                    "total_points": 18,
                    "description": "",
                    "created_at": "2017-05-29 04:54:13",
                    "updated_at": "2017-06-06 01:10:52",
                    "url": "/student/1",
                    "badges": [
                    "dry_master"
                    ]
                },
                {
                    "id": 2,
                    "breathecode_id": 2,
                    "email": "pedro@4geeks.co",
                    "avatar_url": "",
                    "full_name": "Pedro",
                    "total_points": 0,
                    "description": "",
                    "created_at": "2017-05-29 04:54:13",
                    "updated_at": "2017-05-29 04:54:13",
                    "url": "/student/2",
                    "badges": []
                }
            ]
        }

## Student Collection [/student/{id}]

### Get single Student [GET]

+ Response 200 (application/json)

        {
            "code": 200,
            "data": {
                "id": 1,
                "name": "Master in CSS Selectors",
                "created_at": "2014-11-11T08:40:51.620Z",
                "url": "/student/1",
                "avatar": "/path/to/image",
                "total_points": 50,
                "badges": [ 'css_master','copy paster']
            }
        }
        
### Create single Student [POST]

+ Request (application/json)

    + Attributes
        + slug              (string, required) - Slug of the badge
        + name              (string, required) - Name for the badge
        + image_url         (string, required) - Image that points to the url of the user image previously uploaded
        + points_to_achieve (number, required) - Total points acumuled to win it
        + technologies (string, required) - All the technologies separated by comma

+ Response 201 (application/json)

        {
            "code": 200,
            "data": {
                "id": 1,
                "slug": "css_master",
                "name": "Master in CSS Selectors",
                "earned_at": "2014-11-11T08:40:51.620Z",
                "url": "/badge/1",
                "image_url": "/path/to/image",
                "points_to_achieve": 50,
                "technologies": [
                    "js", "swift"
                ]
            }
        }
        
### Delete single student [DELETE]

A student can only be deleted if it has activities with more than 2 days old.

+ Request (application/json)
    + Attributes
        +id            (string, required) - The student id

+ Response 201 (application/json)
        
        {
            "code": 200,
            "message": "ok"
        }

## Activity Collection [/activity/student/{student_id}]

### Get student latest activities [GET]

+ Request (application/json)

    + Attributes
        + student_id            (string, required) - The student id
        + activity_type     (string, required) - It could be project, quiz or challenge
        + badge_slug         (array, required) - A particular set of badge slugs
        
+ Response 201 (application/json)

        [
            {
                "type": "project",
                "name": "Quiz about javascripts babel.js",
                "description": "Loren ipsum orbat thinkin ir latbongen sidoment",
                "creation_at": "2014-11-11T08:40:51.620Z",
                "points_earned" : 3,
                "badge_slug" : "css_master"
            }
        ]

### Add activity to student [POST]

Avery activity that the student does will give him some points, there are 3 types of activies
and they can be done anywhere over the internet.

When an activity is ADDED the points should be sum on each related badge.

+ Request (application/json)

        {
            "student_id": 1,
            "type": "project",
            "name": "Quiz about javascripts babel.js",
            "description": "Loren ipsum orbat thinkin ir latbongen sidoment",
            "creation_at": "2014-11-11T08:40:51.620Z",
            "points": [
                {
                    "slug": "css_master",
                    "amount": 3
                },
                {
                    "slug": "css_master",
                    "amount": 3
                }
            ]
        
        }

+ Response 201 (application/json)

        {
            "code": 200,
            "data": {
            "activity_hash": "9a6747fc6259aa374ab4e1bb03074b6ec672cf99",
            "student_id": 1,
            "type": "project",
            "name": "Quiz about javascripts babel.js",
            "description": "Loren ipsum orbat thinkin ir latbongen sidoment",
            "creation_at": "2014-11-11T08:40:51.620Z",
            "points": [
                {
                    "slug": "css_master",
                    "amount": 3
                },
                {
                    "slug": "css_master",
                    "amount": 3
                }
            ]
        
            }
        }
        
### Delete activity from student [DELETE]

It is possible to delete an activity if for some reason it was added in the last 5 days. Otherwise it will be marked as "archived".

When an activity is deleted the points should be substracted on each related badge.

+ Request (application/json)
    + Attributes
        + activity_hash            (string, required) - The UNIQUE sha1 hash of the activity

+ Response 201 (application/json)
        
        {
            "code": 200,
            "message": "ok"
        }

## Specialty Collection [/specialty/{?id}{?slug}]

### Get single specialty [GET]

+ Parameters
    + slug (string, optional) - Slug of the badge
    + id (string, optional) - Id of the badge

+ Response 200 (application/json)

        {
            "code": 200,
            "data": {
                "id": 1,
                "slug": "front-end",
                "name": "Font-End Developer",
                "url": "",
                "image_url": "https://assets.breatheco.de/img/funny/baby.jpg",
                "description": "You have completed all the front end skills",
                "total_points": 0,
                "created_at": "2017-05-25 14:46:04",
                "updated_at": "2017-05-25 14:46:04",
                "badges": [
                    "dry_master",
                    "clean_code"
                ]
            }
        }
        
### Create specialty [POST]

Having a specialties is the ultimate goal, a specialty is comprised by a group of badges.

+ Request (application/json)

        {
          "profile_slug": "full-stack-web",
          "name": "CSS Master",
          "slug": "css-master",
          "image_url":"",
          "description": "Loren ipsum orbat thinkin ir latbongen sidoment",
          "badges": ["css_selectors","clean_code"],
          "points_to_achieve": 40
        }

+ Response 201 (application/json)

        {
            "code": 200,
            "data": {
                "id": 1,
                "slug": "Clean Code"
                "name": "Quiz about javascripts babel.js",
                "description": "Loren ipsum orbat thinkin ir latbongen sidoment",
                "creation_at": "2014-11-11T08:40:51.620Z",
                "url": "/badge/1",
                "total_points": 3,
                "badges": ["css_master","css_master"]
            }
        }
        
### Delete specialty [DELETE]

When a specialty is deleted the tags are not deleted, and specialties can only be deleted if they don't any activities with more than 5 days old. . Otherwise it will be marked as "archived".

+ Request (application/json)
    + Attributes
        +specialty_slug            (string, required) - The UNIQUE slug for th specialty

+ Response 201 (application/json)
        
        {
            "code": 200,
            "message": "ok"
        }