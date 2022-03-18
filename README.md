![](https://www.expresstechsoftwares.com/wp-content/uploads/memberpress_discord_addon_banner.png)

# [Connect MemberPress to Discord](https://www.expresstechsoftwares.com/step-by-step-documentation-guide-on-how-to-connect-memberpress-and-discord-server-using-discord-addon) #
![](https://img.shields.io/badge/build-passing-green) ![License](https://img.shields.io/badge/license-GPL--2.0%2B-red.svg)

### Welcome to the ExpressTech MemberPress Discord Add On GitHub Repository

This add-on enables connecting your MemberPress enabled website to your discord server. Now you can add/remove MemberPress members directly to your discord server roles, assign roles according to your member levels, unassign roles when they expire, change role when they change membership.

# [Step By Step guide on how to set-up plugin](https://www.expresstechsoftwares.com/step-by-step-documentation-guide-on-how-to-connect-memberpress-and-discord-server-using-discord-addon)


## Installation
- You can find the plugin inside the MemberPress settings Add-ons and click install from there
- OR Upload the `memberpress-discord-addon` folder to the `/wp-content/plugins/` directory.
- Activate the plugin through the 'Installed Plugins' page in WordPress admin.

## Connecting the plugin to your Discord Server.
- Inside WP Admin, you will find Discord Settings sub-menu under top-level MemberPress menu in the left hand side.
- Login to your dsicord account and open this url: https://discord.com/developers/applications
- Click Top right button "New Appliaction", and name your Application.
- New screen will load, you need to look at left hand side and see "oAuth".
- See right hand side, you will see "CLIENT ID and CLIENT SECRET" values copy them.
- Open the discord settings page.
- Paste the copied ClientID and ClientSecret.
- Add a Redirect URL, this should be MemberPress Profile of members. 
- Bot Auth Redirect URL: Add this URL inside Application Redirect settings.
- Now again see inside discord left hand side menu, you will see "Bot" page link.
- This is very important, you need to name your bot and click generate, this will generate "Bot Token".
- Copy the "Bot Token" and paste into "Bot Token" setting of Discord aa-on Plugin.
- Now the last and most important setting, "Server ID".
- - Open https://discord.com/ and go inside your server.
- - Enable Developer mode by going into Advanced setting of your account.
- - Then you should right click on your server name and you will see "Copy ID"
- - Copy and paste into "Guild ID" Settings
- Now you will see "Connect your bot" button on your plugin settings page.
- Click Connect your bot button and this will take you to the Discord authorisation page.
- Here you need to select the Server of which Guild ID you just did copy in above steps.
- Once successfully connect you should see Bot Authorized screen.
- Open again the discord server settings and see Roles menu.
- Please make sure your bot role has the highest priority among all other roles in your discord server roles settings otherwise you will see 5000:Missing Access Error in your plugin error logs.

## Some features
This plugin provides the following features: 
1) Allow any member to connect their discord account with their MemberPress membership account. 
2) Members will be assigned roles in discord as per their membership level.
3) Members roles can be changed/remove from the admin of the site.
4) Members roles will be updated when membership expires.
5) Members roles will be updated when membership cancelled.
6) Admin can decide what default role to be given to all members upon connecting their discord to their membership account.
7) Admin can decide if membership should stay in their discord server when membership expires or cancelled.
8) Admin can decide what default role to be assigned when membership cancelled or expire.
9) Admin can change role by changing the membership by editng user insider WP Manage user.
10) Send a Direct message to discord members when their membership has expired. (Only work when allow none member is set to YES and Direct Message advanced setting is set ENABLED)
11) Send a Direct message to discord members when their membership is cancelled. (Only work when allow none member is set to YES and Direct Message advanced setting is set ENABLED)
12) Send membership expiration warnings Direct Message when membership is about to expire (Default 7 days before)
13) Short code [mepr_discord_button] can be used on any page to display connect/disconnect button.
14) Using the shortcode [mepr_discord_button] on any page, anyone can join the website discord server by authentication via member discord account. New members will get `default` role if selected in the setting.
15) Button styling feature under the plugin settings.

## Solution of Missing Access Error
- Inside the log tab you will see "50001:Missing Access", which is happening because the new BOT role need to the TOP priroty among the other roles.
- - The new created BOT will add a ROLE with the same name as it is given to the BOT itself.
- So, Go inside the "Server Settings" from the TOP left menu.
- Go inside the "Roles" and Drag and Drop the new BOT over to the TOP all other roles.
- Do not for forget to save the roles settings

# Fequently Asked Questions
- I'm getting an error in error Log 'Missing Access'
- - Please make sure your bot role has the highest priority among all other roles in your discord server roles settings.
- Role Settings is not appearing.
- - Clear browser cache, to uninstall and install again.
- - Try the disabling cache
- - Try Disabling other plugins, there may be any conflict with another plugin.
- Members are not being added spontaneously. 
- - Due to the nature of Discord API, we have to use schedules to precisely control API calls, that why actions are delayed. 
- Member roles are not being assigned spontaneously.
- - Due to the nature of Discord API, we have to use schedules to precisely control API calls, that why actions are delayed. 
- Some members are not getting their role and there is no error in the log.
- - Sometimes discord API behaves weirdly, It is suggested to TRY again OR use another discord account.
- After expiry or member cancellation the roles are not being removed
- - It is seen in discord API that it return SUCCESS but does not work sometimes. It is suggested to manually adjust roles via MemberPress->Members->Members table.
