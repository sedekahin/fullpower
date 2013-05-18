=== Plugin Name ===
Contributors: offlinetools
Donate link: http://offlinemarketingtools.com
Tags: policies, disclaimer, privacy, terms
Requires at least: 2.9.2
Tested up to: 2.9.2
Stable tag: 1.00

WP Policies allow you to quickly add privacy policy statements to your blog.

== Description ==

WP Policies allows you to quickly add pre-written privacy policy and disclaimer statements to your Wordpress blog. 
The plugin currently comes with 10 policies that you can edit depending on your blog.

== Installation ==

1. Upload entire wp-policies folder to /wp-content/plugins/
2. Set permission of folder /wp-policies/wp-static/ to 777.
3. Activate the plugin from your Wordpress PLUGINS page.
4. Go to SETTINGS > WP POLICIES:
   * Fill in the CONTACT DETAILS section at the bottom of the settings page. Put your Company Name in ALL CAPS.
   * Click IMPORT at the bottom of the page to import the default policies.
   * Read over each default policy as some may not apply to your business. Edit where necessary.
   * Delete or deactivate the pages you do not want to display by setting them to DRAFT in WP Page Manager.

5. Go to the WP Page Manager and make sure that the comments are turned off for your pages.
6. Add `<?php static_footer_pages(); ?>` to the footer.php file of your theme.

= Usage Instructions =

* After activation, the plugin allows you to import several default privacy policy pages to your blog. 
* Default pages are located in /wp-static/source/ folder as .txt files.
* Once imported, these pages are listed in the PAGES section of your blog, however they are hidden and not listed in the public PAGES area.
* Each NEW page that you create displays content from an associated .dat file that is created in /wp-policies/wp-static/

= Displaying Policy Page Links =

* To display policy page links in the footer of your blog:
  - place this code in footer.php file of your theme: `<?php static_footer_pages(); ?>`

= Add/Delete/Disable Pages =

* To add new policy pages, go to SETTINGS > WP POLICIES
  - There you can edit an existing page, or add a new page. 
  - When adding a page file name be sure to use a unique name. 
  - All page content entered for new pages must be in HTML
  - The NEW page file is saved in the /wp-static/ folder as name.dat
  - Once you save a page, its link will be displayed in the footer automatically until you disable it by setting the page to DRAFT.

* To disable a specific policy page:
  - Go to SETTINGS > WP POLICIES and click on the MANAGE link for the page you wish to disable. 
  - Then from there you would edit the page by setting it to DRAFT.

* To delete the policy page:
  - Go to SETTINGS > WP POLICIES and click the DELETE link for the item you wish to delete. 
  - Note that deleting will also delete the corresponding page from your blog.


= Adding Dynamic Content =

*  You can display the contents of a policy page separately on post pages using <!-- filename.dat --> inside the post content area.
* To add dynamic content to your policy pages, you can use any of the dynamic fields listed in the Contact Details section. Just insert them into the body of your content page.


= Moving Files =

* Our default policy pages are saved in /wp-static/source/ folder as .txt files
* Any new policy pages you may have added are saved in /wp-static/ folder as .dat files

* To move your NEW policy pages to a new blog:
  1. Download the .dat files you want to use from the /wp-static/ folder
  2. Change the extension of each file to .txt
  3. Now upload the original plugin files to the new blog.
  4. Go to the /wp-static/source/ and add your new .txt files.
  5. set permission of /wp-static/ to 777, 
  6. Activate the plugin and then import your policies.


== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

= 1.00 =
* Initial Release: 03/10/2010