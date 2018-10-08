=== Hlasovani projektech  ===
Contributors: Otevreny Urad/ M.Stastny
Tags: partcipation, project, voting, municip`ality
Requires at least: 4.4
Tested up to: 4.9.8
Stable tag: trunk
License: AGPLv3
License URI: http://www.gnu.org/licenses/agpl-3.0.en.html

Direct citizen-government communication & collaboration.

== Description ==

PB Voting is a plugin for distributing registration tokens used on voting on LimeSurvey platform.
Gets token form LImesurvey and sends them either by email or SMS to particopant.
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
9. Create new post of the type hlasovani

===== How to use it ======
Precondition - exists post type hlasoani, taxonomy voting%status.
The widget is call by shortcode
example: do_shortcode('[pb_vote_reg_widget voting_id="123456" force_display=true]')
Can be called without parameters. Then voting_id is read from current active post
and displayed according to voting_status


New version task by 2018/08/13
= rozsirit taxo se stavy o info jestli se maji zobrtazik projektym, zobrazit tlacitko pridat novy projekt, zobrazit lint na stranku s hlasovanim
