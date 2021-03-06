=== Hlasovani projektech  ===
Contributors: Otevreny Urad/ M.Stastny
Tags: participation, project, voting, municipality
Requires at least: 4.4
Tested up to: 5.2.2
Stable tag: trunk
License: AGPLv3
License URI: http://www.gnu.org/licenses/agpl-3.0.en.html

Direct citizen-government communication & collaboration.

== Description ==

PB Voting is a plugin for distributing registration tokens used on voting on LimeSurvey platform.
Gets token form LImesurvey and sends them either by email or SMS to participant.
Can be used as well with local wordpress database

== Installation ==

1. Install the plugin through the WordPress plugins screen directly or upload the plugin files to the `/wp-content/plugins/plugin-name` directory.
2. Install plugin Pods - Custom Content Types and Fields through the WordPress plugins
3. Activate both plugins through the 'Plugins' screen in WordPress.
4. Actvivate component "Migrate packages" in the PODS component settings
5. Import new pods type definition "hlasovani" saved in file "./data/pods_pbvoting_def.txt".
6. Set plugin constant in the file vp_voting.php
    c. DELIVERY_MSG_TYPE - Email | Sms

7. Set plugin constants in the file wp_config.php
    a. GOOGLE_CAPTCHA_SITE_KEY - from Google captcha setting
    b. GOOGLE_CAPTCHA_SECRET_KEY - from Google captcha setting
    c. LIMESURVEY_LOGIN - from Limesurvey
    d. LIMESURVEY_PASSWORD- from Limesurvey
    e. SMSGATE_LOGIN
    f. SMSGATE_PASSWD
8. Add new voting statuses with slugs novy, aktivni, pozastaveny, ukonceny,...
    set term_meta "allow_voting" and "allow_adding_project" true/false for each term
8. Add new voting categories with any values
9. Create new post of the type hlasovani

===== How to use it ======
Precondition - exists post type hlasovani, taxonomy voting_status, voting_category.
The widget is call by shortcode
examples:
do_shortcode('[pb_vote_reg_widget voting_id="123456" force_display=true]')
do_shortcode('[pb_vote_reg_widget voting_slug="voting-post-name"]')
Can be called without parameters. Then voting_id is read from current active post
and displayed according to voting_status

The generated tokens are sent by email or text messages. The text message is defined in post's meta field.
The message can have these placeholders tha are replace by values:
{#token}, {#expiration_time}, {#survey_url}, {#code_spell}

How to call shortcode for new project form examples
[pbvote_project_insert voting_id="1379" voting_slug="hlasovani-2020" ]

How to call shortcode for export page [pbvote_projects_export]
