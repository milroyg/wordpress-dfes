# Elementor Addon Components
Contributors: EAC Team
Tags: page-builder, elementor, component, addon, widget, dynamic tags, custom CSS, template, image, OpenStreetMap, WooCommerce
Requires at least: 6.5.0
Tested up to: 7.0.0
Stable tag: 2.5.2
Requires PHP: 7.4
Requires Plugins: elementor
Elementor tested up to: 4.1.3
WC requires at least: 8.0.0
WC tested up to: 9.8.0
License: GPLv3 or later License
License URI: [GPL licenses](http://www.gnu.org/licenses/gpl-3.0.html)
**Elementor Addon Components** is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE
See the GPL General Public License for more details.

## Description
* The EAC plugin extends Elementor's widgets and adds advanced features for Elementor.
* In particular the standard dynamic tags, those for ACF and WooCommerce and implements the functionality to associate CSS code, custom attributes with a widget or the current page.

## Requirements
* WordPress 6.5 tested up to 7.0.0
* Elementor 3.28.0 tested up to 4.1.3
* PHP version 7.4 tested up to 8.3.14
* MySQL version 5.0 or greater

## Installation
* The plugin Elementor is installed and active
* Extract the zip file and drop the contents in the *wp-content/plugins/* directory of your WordPress installation
* Activate it through the Plugins menu in WordPress

## Components
* You can Activate/Deactivate each of the components/features in the **EAC Components** settings page to avoid loading unnecessary resources
* If you deactivate all the components, you will still keep the features

## Languages
* English (en_US) is the default language of the plugin
* French (fr_FR) language support
* Spanish (es_ES) language support
* Italian (it_IT) language support
* Hindi (hi_IN) language support
* RTL languages support

## Frequently Asked Questions

## Screenshots

## Changelog

# V2.5.2 - 06/20/2026
* Fix: the word count for the product description is not being handled correctly.
* Improve: optimizing queries for the 'Element Usage' feature.
* Improve: compatibility with WordPress 7.0.0
* Improve: compatibility with Elementor 4.1.3

# V2.5.1 - 06/14/2026
* New: added a custom Gutenberg block to display the Weather forecast for a city (Read more).
* Fix: incorrect test for Repeater block subfields FAQ.
* Improve: compatibility with Elementor 4.1.1

# V2.5.0 - 04/27/2026
* New: added a custom Gutenberg block to publish the ACF relationship field (Read more).
* Fix: Invalid URL for the EAC components menu icon

# V2.4.9 - 04/09/2026
* Update: the ACF blocks (Gallery, Repeater) are now standalone and can be loaded independently of Elementor.
* Update: 'ACF repeater block' uses FormTokenField control instead of the custom Select2 control.
* Updated: 'ACF repeater field' now support ACF version 6.7 features including improved nonce verification.
* Improve: compatibility with Elementor 4.0.1

# V2.4.8 - 03/30/2026
* New: added a custom Gutenberg block to publish the ACF gallery field (Read more).
* Update: 'ACF repeater block' the ACF File field opens PDF files in a viewer.
* Update: 'ACF repeater block' added a contrast checker for color settings.
* Update: 'ACF repeater block' uses the REST API instead of AJAX requests.
* Fix: 'ACF repeater block' images are not displayed first.
* Fix: 'ACF repeater block' showing/hiding of the Image and Link sections is not being handled correctly.
* Security Fix: dynamic Woocommerce tag 'Description' output not sanitized.
* Security Fix: widget 'Search' the number of characters entered is not checked. 
* Improve: compatibility with Elementor 3.35.6

# V2.4.7 - 02/16/2026
* New: added a custom Gutenberg block to publish the ACF repeater field (Read more).
* Fix: the default image size of the ACF gallery field is not compliant.
* Update: continuously improve the accessibility of components.
* Improve: compatibility with Elementor 3.34.1

# V2.4.6 - 12/28/2025
* New: Italian language support (it_IT) has been added. Translation done by AI. Feel free to edit the translation file in the Languages ​​directory.
* New: Hindi language support (hi_IN) has been added. Translation done by AI. Feel free to edit the translation file in the Languages ​​directory.
* Update: 'ACF relationship grid' added a fallback field.
* Fix: 'RSS feed' accessibility, the 'Read the feed' button does not open the link with the space bar.
* Fix: 'ACF repeater' the button URL is truncated in grid mode.
* Improve: compatibility with WordPress 6.9
* Improve: compatibility with Elementor 3.33.6

# V2.4.5 - 11/11/2025
* New: Spanish language support (es_ES) has been added. Translation done by AI. Feel free to edit the translation file in the Languages ​​directory.
* Update: default language set to English. All widget strings swapped and French translations added/adjusted.
* Improve: accessibility for the product grid component.
* Improve: compatibility with Elementor 3.32.5 - Editor V4 inactive

# V2.4.4 - 10/19/2025
* Improve: accessibility for HTML sitemap, Table of contents, ACF repeater, Post and product grid, Image galleries.
* Improve: compatibility with Elementor 3.32.3 - Editor V4 inactive.
* Improve: few minor bug fixes.

# V2.4.3 - 09/28/2025
* New: EAC now requires Elementor version 3.28.0 or newer.
* Update: loading scripts and styles at control level (Read this carefully).
* Update: the Repeater File subfield now opens in a modal box (Only supports PDF files).
* Fix: PHP 8.4 deprecation notices related to 'nullable type with null as default parameter value'.
* Improve: compatibility with Elementor 3.31.5 Editor V4 inactive.
* Improve: accessibility 'ACF repeater' FAQ mode.
* Improve: few minor bug fixes.

# V2.4.2 - 09/07/2025
* New: ACF field type 'EAC repeater' to manage repetitive content.
* New: 'ACF Repeater' component to display the contents of an ACF Repeater field.
* Improve: compatibility with Elementor 3.31.3 Editor V4 inactive

# V2.4.1 - 08/08/2025
* Update: remove advanced 'Settings/Query filter/Queries' features of the 'Post and product grid' components and revamp settings section.
* Update: added autocompletion to the 'Search' component.
* Improve: optimization of script and style loading.
* Improve: compatibility with Elementor 3.30.3 Editor V4 inactive.
* Improve: few minor bug fixes.

# V2.4.0 - 07/16/2025
* Fix: PHP 7.4 __clone() cannot declare a return type.

# V2.3.9 - 07/12/2025
* New: added a new 'Advanced Product Grid' component to display bestsellers, recently sold and featured products.
* Fix: Select2 custom control with tags (terms) displays incorrect list.
* Improve: few minor bug fixes.
* Notice: the advanced 'Settings/Query filter/Queries' features of the 'Post and product grid' components will be removed with the next version (v2.4.0) due to the obsolescence of their uses.

# V2.3.8 - 06/27/2025
* Fix: critical error in System info if Zend OPcache is not active.
* Fix: image disappears from DOM after closing lightbox.
* Notice: the advanced 'Settings/Query filter/Queries' features of the 'Post and product grid' components will be removed with the version 2.4.0

# V2.3.7 - 06/17/2025
* Update: added custom CSS feature at the page settings level for header and footer templates.
* Update: adds a new option 'Access to edit content only' for role management.
* Update: added a new OPcache information section under the System info tab.
* Fix: product taxonomy list is wrong for Woocommerce dynamic tags.
* Improve: compatibility with Elementor 3.29.2

# V2.3.6 - 06/09/2025
* Update: consolidation of namespaces, PHP file names and class names to implement class autoloading.
* Enhance: automatic class autoloading implementation.
* Update: the code and style of the 'Read more' button are now reusable across different widgets.
* Update: added a dynamic tag (Audio) to load an MP3 audio file into the Webradio widget.
* Fix: 'Display conditions' category list is empty.
* Improve: few minor bug fixes.

# V2.3.5 - 05/07/2025
* Fix: plugin update module causes slow dashboard loading.
* Fix: itemprop and itemscope attributes are not displayed in the 'nav' tag of the navigation menu.
* Enhance: page loading speed by compressing the HTML code produced by various widgets.
* Improve: compatibility with WordPress 6.8.1
* Improve: compatibility with Elementor 3.28.4

# V2.3.4 - 04/23/2025
* Update: 'ACF options page' can now be viewed by the Author role.
* Update 'upload JSON files' integration of rights granted in Elementor's role management module.
* Notice: feature option 'Upload unfiltered files' is removed.
* Fix: overlapping images for Masonry mode display with Firefox browser.
* Improve: DOM optimization, remove additional wrappers for grids and galleries.
* Improve: few minor bug fixes.
* Improve: compatibility with Elementor 3.28.3

# V2.3.3 - 02/28/2025
* Update: added animation duration option for 'Modal box' component.
* Update: the plugin settings page now checks for changes before closing. 
* Security Fix: saving 'Element usage & Media custom fields' options in the database.
* Improve: few minor bug fixes.
* Improve: compatibility with Elementor 3.27.6

# V2.3.2 - 02/07/2025
* Update: refactored of the 'Table of contents' component and addition of new options to enhance the user experience.
* Improve: compatibility with Elementor 3.27.1

# V2.3.1 - 01/14/2025
* New: Wordpress pagination feature for the 'Post grid' component.
* New: Wordpress pagination feature for the 'Product grid' component.
* Update: added an attribute to the ACF shortcode 'Image gallery' to open images in a Lightbox.
* Update: added an attribute to the ACF shortcode 'Image gallery' to publish a gallery of a user's profile.
* Improved: 'Justify' mode display for the 'Advanced image gallery' component.
* Fix: margin between elements causes overlapping bullets or pagination for the image Slider.
* Fix: accessibility links/buttons with the image Slider using the TAB key.

# V2.3.0 - 12/31/2024
* New: ACF field type 'EAC gallery' for managing a collection of images.
* New: dynamic ACF tag 'Image gallery' to create a gallery with the new ACF field type 'Image gallery'.
* Fix: accessibility of grids and galleries using the TAB key.
* Fix: prevents cell phone harvesting from spam bots but accessible to human beings.
* Improve: compatibility with Elementor 3.26.2

# V2.2.9 - 11/27/2024
* New: 'Memory usage' component to monitor and display memory usage and page load times
* Update: added a new 'System info' tab to the plugin page settings
* Improve: move label initialization to fix _load_textdomain_just_in_time notice
* Improve: compatibility with WordPress 6.7.1
* Improve: compatibility with Elementor 3.25.10

# V2.2.8 - 11/17/2024
* Updated: Implementation of logical CSS properties for all components to support RTL languages
* The changes concern the plugin settings page, the Elementor editor and rendering in the frontend
* Updated: refactored of the 'HTML sitemap' component interface and added new features for improved rendering
* Improvement: prevents email addresses harvesting from spam bots but accessible to human beings

# V2.2.7 - 10/14/2024
* New: 'Countdown' component that allows you to create static countdown or perpetual countdown (Evergreen).
* Updated: compliance of all components with the v3.24 style loading strategy.
* Updated: refactored of the 'RSS reader' component interface.
* Updated: refactored of the 'WebRadio feeds' component interface.
* Fix: dynamic ACF 'Date time' tag does not display date/time correctly for the selected output format.
* Security fix: Improved code security enforcement by removing e-mail address from social media widgets.
* Improve: implementation of logical CSS properties for RTL languages in progress.
* Improve: compatibility with Elementor 3.24.3
* Improve: compatibility with Wordpress 6.6.2

# V2.2.6 - 08/27/2024
* New: help added for 'Element usage' feature.
* Fix: 'Element usage' count of publications displayed in the Elementor editor and Element Manager.

# V2.2.5 - 08/07/2024
* New: 'settings page' add a count of publications using each component.
* Updated: replace the deprecated Schemes color and typography arguments by Global color and typography.
* Updated: enable SVG upload option for Icons control.
* Fix: 'Navigation menu' body page scroll on mobile.
* Enhance: 'Page load speed' add 'defer' attribute to non-essential Javascript scripts.
* Improve: compatibility with WordPress 6.6.2
* Improve: compatibility with Elementor 3.23.4

# V2.2.4 - 07/21/2024
* Updated: added an option to create a global link for each grid element.
* Updated: added 'Relative' option position for 'Off-canvas' component.
* Fix: the dynamic ACF datetime tag is translated into the wrong language.
* Fix: in some environments, the plugin language is displayed in French.
* Fix: taxonomy filter does not appear with mobiles for all grids.
* Improve: form security for the plugin settings page.
* Improve: component interfaces in the editor have been refactored to improve design and overall consistency.
* Improve: compatibility with the experiment 'Inline font icons'.

# V2.2.3 - 05/21/2024
* New: dynamic Woocommerce tag 'Recent sales gallery' to create a gallery of recent product sales.
* Updated: migration of the 'Swiper' library to version 9.4.1 for components with the Slider display mode.
* Updated: migration of the 'Prism' library to version 1.29.0 for the component 'Syntax highlighter'.
* Improve: the plugin is now compatible with WooCommerce HPOS 'High-Performance Order Storage'.
* Improve: compatibility with Elementor 3.21.4
* Improve: compatibility with WordPress 6.5.3
* Notice: component 'Image effects' is removed.

# V2.2.2 - 05/02/2024
* Notice: all the changes in this version are aimed at speeding up page loading, but also have an impact on the design of the widgets used.
* New: 'Page preloading' feature to preload pages from their links (Enable option in Dashboard/EAC settings/Wordpress).
* New: dynamic Woocommerce tag 'Categories gallery' to create a gallery with images related to categories.
* New: dynamic Woocommerce tag 'Featured gallery' to create a gallery with featured images.
* New: dynamic Woocommerce tag 'Best sellers gallery' to create a gallery of best-selling products.
* New: dynamic Woocommerce tag 'Similar gallery' to create a gallery of products similar to another.
* Updated: standardization of 'Grid and Grid equal height' display modes.
* Updated: refactored the old 'padding-bottom' image ratio technique with 'aspect-ratio' CSS.
* Updated: image element now supports 'srcset and size' attributes for better responsiveness.
* Updated: added a 'loading lazy' option that can be activated with grids containing images.
* Notice: the old and basic component 'Image effects' will be remove from the next version.

# V2.2.1 - 04/11/2024
* Fix: 'Image gallery' critical error on image link.

# V2.2.0 - 04/09/2024
* New: 'Advanced image gallery' lets you create responsive image galleries from multiple sources, five display modes, lightbox and more.
* New: dynamic tag 'Post gallery' to create a gallery with images attached to the current post.
* New: dynamic Woocommerce tag 'Category gallery' to create a gallery with images from a category.
* New: dynamic Woocommerce tag 'Product category URL' to retrieve the URL of a category.
* Updated: 'Image gallery' the interface has been completely rewamped.
* Updated: feature 'Link element' now supports custom attributes.
* Updated: 'Swiper slider' uses 'aspect-ratio' CSS instead of the old 'padding-bottom' technique for images.
* Fix: feature 'Link element' assignment to constant variable.
* Fix: accessibility 'Table of content' no aria attribute on pictograms.
* Improve: compatibility with Elementor 3.20.1
* Notice: WooCommerce HPOS 'High-Performance Order Storage' incompatibility notice added.

# V2.1.9 - 03/21/2024
* Fix: 'Header Footer builder' generates two head tags for unsupported themes.

# V2.1.8 - 03/15/2024
* New: 'Custom CSS' feature added global custom CSS to the site.
* New: added an indicator in the navigator to shwo that the element has Custom CSS applied to it.
* New: added an indicator in the navigator to show that the element has display conditions applied to it.
* New: Elementor, ACF and Woocommerce dynamic tags are compatible with the PRO version of Elementor.
* Updated: The list of dynamic ACF tags is displayed by ACF group name.
* Improve: compatibility with Elementor 3.19.2

# V2.1.7 - 02/12/2024
* New: added new feature 'Display conditions' on section, container and widget to hide elements according to simple rules.
* New: added two new dynamic ACF tags 'ACF Date time & ACF group Date time' with Date picker field.
* Updated: dynamic 'shortcode' tag added escape filter.
* Fix: 'Custom CSS' with PHP 8.1.x trim function passing null to parameter.
* Improve: accessibility 'Ken Burns effect' consolidates rules relating to navigation with screen readers.

# V2.1.6 - 01/10/2024
* Updated: 'Single menu' added option to display menu items in multiple columns as Mega menu.
* Updated: dynamic tag 'Elementor templates' added 'Container' in the item selection list.
* Fix: dynamic tag 'Acf text field' no longer in the list of dynamic tags.
* Fix: 'Pinterest feeds' the fields are not emptied when the feed is modified.
* Improve: prevent security vulnerabilities when the HTTP request is being made to an arbitrary URL.
* Improve: accessibility 'Simple menu' consolidates rules relating to navigation with screen readers.
* Improve: compatibility with WordPress 6.4.2
* Improve: compatibility with Elementor 3.18.2

# V2.1.5 - 12/19/2023
* Updated: accessibility 'Openstreetmap' setting up rules for navigating between elements with the keyboard and screen readers.
* Updated: accessibility certain modifications may have an impact on the existing CSS, notably the buttons.
* Updated: start of the migration of Javascript handlers with the method recommended in the Elementor 3.10 version.
* Updated: 'ACF Relationship grid' delete the selection list of post types.
* Fix: accessibility 'Off-canvas' keyboard focus is not trapped in content when the Off-Canvas is open.
* Fix: 'Off-canvas' on mobiles 'body' scrolling is not always disabled when Off-canvas is open.

# V2.1.4 - 12/01/2023
* Updated: 'Lottie background' added "Loop" option to trigger the animation once or loop (Default).
* Updated: 'RSS reader' prevent PHP directive 'allow_url_fopen' from causing an exception.
* updated: 'RSS reader' added an option (Content: vertical alignment) to better manage the space between elements.
* Updated: 'Openstreetmap' prevent PHP directive 'allow_url_fopen' from causing an exception.
* Updated: 'PDF viewer' prevent PHP directive 'allow_url_fopen' from causing an exception.
* Improve: accessibility 'Simple menu' implementation of rules relating to navigation with the keyboard and screen readers.
* Improve: accessibility 'Grid Load more' button the focus is on the first focusable element of the new items.
* Improve: accessibility 'Grid Load more' button displays the number of loaded items out of the total number of expected items.
* Notice: to increase the audience, visibility and ranking (SEO) of our work, it would be great if you put a link from your site to our site.

# V2.1.3 - 11/15/2023
* Updated: the 'Clone, Create, Trash, Edit with Elementor' actions of header/footer templates are now relative to the rights defined in the 'Role Manager' module of Elementor.
* Updated: 'Simple nav menu' added option to enable/disable overflow.
* Updated: 'Image gallery, Post & product grid' added an option in grid mode to adjust each row to the same height.
* Updated: 'Image gallery, Post & product grid' filters are fully customizable.
* Fix: 'ACF Relationship grid' doesn't work with Header and Footer templates.
* Fix: 'Sticky effect' Top/Bottom thresholds can be null.
* Fix: 'Table of content' does not jump to section on first touch with mobiles.
* Improve: accessibility for readers with disabilities.
* Improve: accessibility add outline focus for focusable elements.
* Notice: these changes have a potential of breaking somme components CSS.
* Improve: loading shared scripts 'Swiper & RSS feed' with ES6 module Export/Import statements.
* Improve: compatibility with WordPress 6.3.2
* Improve: compatibility with Elementor 3.16.0
* Notice: 'Openstreetmap' since end of October 2023 Stamen tile service is closed. "Toner, Tonerlite & Terrain" requires an API key to work (Stadia).

# V2.1.2 - 08/07/2023
* Fix: 'Breadcrumbs' widget critical error with Yoast SEO.
* Fix: Showing product description of 'Product Grid' widget does not check if woocommerce is still active.
* Fix: 'Chart' widget does not use a strict comparison to check an external URL.
* Fix: 'ACF Relationship Grid' widget title style disappears when featured image option is disabled.

# V2.1.1 - 07/24/2023
* New: added 'Breadcrumbs' widget for the 'Header & Footer builder' feature.
* New: added 'Reading progress bar' widget for the 'Header & Footer builder' feature.
* New: added 'Unfiltered medias' feature to improve security when adding external JSON URL for Openstreetmap and Lottie widgets (Settings page 'EAC components/WordPress' tab).
* Updated: 'Image gallery' inline editing of button label, description and title.
* Updated: 'Team members' inline editing of name, job title and biography.
* Updated: Slider mode now supports image centering.
* Fix: 'Simple menu' appears briefly when the responsive device 'Hamburger menu' is triggered.
* Fix: 'PDF viewer' button icon not displayed.
* Fix: 'PDF viewer' button or text alignment is not correct.
* Improve: added lazyload for images loaded in main components like Post grid, Product grid, Image gallery, ACF relationship, Team members.
* Improve: navigation menu display for header and footer builder is optimized.
* Improve: compatibility with Elementor 3.14.1
* Notice: 36 components and 17 features always available for free.

# V2.1.0 - 06/13/2023
* Notice: due to a big change with the new features, the plugin requires at least WordPress 5.9 and PHP 7.4
* New: added 'Header & Footer' feature will allows you to build and design your own headers and footers.
* New: added basic widgets to help you create your headers and footers (Simple menu, Site and page title, Social media, Search form and Copyright). 
* Updated: 'Openstreetmap' added fullscreen control.
* Updated: 'RSS reader' added control to change the button label.
* Updated: 'Sticky element' script code refactoring to take into account the new feature of building a header.
* Fix: 'HTML sitemap' post settings does not retrieve taxonomy.
* Fix: 'Off canvas' the icon is not displayed when the trigger is a button.
* Fix: 'Author infobox' does not appear with the selected post type.
* Improve: compatibility with Elementor 3.12.2 and WordPress 6.2.2

# V2.0.2 - 03/03/2023
* New: 'Effect Ken Burns' is a new feature that allows you to create a background slideshow with a Ken Burns effect for each image.
* Updated: 'ACF relationship grid' now supports global ACF fields created with the feature Options pages.
* Fix: 'ACF relationship grid' WooCommerce product image not loading.
* Improve: 'ACF relationship grid' Improve content display in grid or slider mode by adding a vertical align control.
* Updated: 'Image hotspots' added new controls to manage the image. Default values ​​can impact the existing.
* Fix: 'Modal box' the contents of block templates appear briefly when the page loads.
* Fix: 'Openstreetmap' default configuration tiles file does not load correctly.
* Fix: 'Openstreetmap' on Safari the click on the markers is inoperative.
* Fix: 'Product grid' displays the sold quantity of the product even if the product is out of stock.
* Improve: compatibility with Elementor 3.10.2
* Notice: minimum Elementor version expected 3.5.0
* Notice: old components 'Background slideshow' and 'Ken Burn slideshow' are removed from this release.

# V2.0.1 - 01/20/2023
* Updated: 'Product grid' add a configuration tab in the plugin settings page for better integration with WooCommerce.
* Fix: critical error user logged in as 'editor' and plugin settings option 'Wordpress/Grant access Options Page' enabled.
* Fix: 'Product grid' dynamic tags are again available for text fields.
* Fix: Components whose dependencies are not active (ACF, WooCommerce) are no longer visible in the plugin settings page.
* Updated: improve the coding of PHP files with PHP coding standards.

# V2.0.0 - 01/01/2023
* New: 'Multiple background images' Add multiple background images in the elements like Container/Section/Column.
* Fix: 'Lottie background' Lib jQuery not loading.
* Fix: 'Post grid' mode slider The avatar covers the entire container under certain conditions.
* Notice: Elementor 3.8.0 Important note if you are using the feature 'EAC custom CSS'. Please check the developer note chapter 'Updated Class Names'.
* Notice: 27 components and 15 features always available for free.
 
## By EAC Team
