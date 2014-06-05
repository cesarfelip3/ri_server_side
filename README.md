ri web
=======

development
===========

here *server* refer to "push notification server"

// Sprint A
// Saving user infomation and push notification dev token for the user

1. when user logged, the app will send "username, email" and dev_token file to server
2. server will save user to db if it doesn't exist, and save dev_token file to server
3. server will get user uuid if it does exist, and remove the previous dev_token, save the new dev_token

// Sprint B
// Saving alert from user
// Send notification to app

1. The app will send "alert" (create time, alert time) to server, when user create an alert in app
2. The server will save the alert (create time, alert time, server create time) for the user
3. There is a cron job in server to query all alerts from different users, and :

    latency = (server create time - create time) * 2 ~= network latency
    current time = time () - latency
    current time >= alert time ==> send the notification to user

// Sprint C
// Saving appointment from user
// Send notification to app


// Spring D
// Send notification

1. cron job ==> query ==> send

progress
=========

