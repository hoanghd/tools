<module>
	<data>
		<module_id>pages</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>1</is_menu>
		<menu><![CDATA[a:2:{s:29:"pages.admin_menu_add_category";a:1:{s:3:"url";a:2:{i:0;s:5:"pages";i:1;s:3:"add";}}s:34:"pages.admin_menu_manage_categories";a:1:{s:3:"url";a:1:{i:0;s:5:"pages";}}}]]></menu>
		<phrase_var_name>module_pages</phrase_var_name>
		<writable />
	</data>
	<menus>
		<menu module_id="pages" parent_var_name="" m_connection="main" var_name="menu_pages_pages_fad58de7366495db4650cfefac2fcd61" ordering="4" url_value="pages" version_id="3.0.0beta1" disallow_access="" module="pages" />
		<menu module_id="pages" parent_var_name="" m_connection="pages.index" var_name="menu_pages_create_a_page_42a6b1a9f5d9d3cb02b85677b552fda0" ordering="103" url_value="pages.add" version_id="3.0.0beta1" disallow_access="" module="pages" />
	</menus>
	<blocks>
		<block type_id="0" m_connection="pages.view" module_id="pages" component="photo" location="1" is_active="1" ordering="1" disallow_access="" can_move="0">
			<title><![CDATA[Pages Photo &amp; Menu]]></title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="pages.view" module_id="pages" component="like" location="1" is_active="1" ordering="2" disallow_access="" can_move="0">
			<title>Pages Likes/Members</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="pages.view" module_id="pages" component="menu" location="1" is_active="1" ordering="3" disallow_access="" can_move="0">
			<title>Pages Mini Menu</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="pages.index" module_id="pages" component="category" location="1" is_active="1" ordering="3" disallow_access="" can_move="1">
			<title></title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="profile.info" module_id="pages" component="profile" location="2" is_active="1" ordering="2" disallow_access="" can_move="1">
			<title>Pages</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="pages.view" module_id="pages" component="widget" location="3" is_active="1" ordering="4" disallow_access="" can_move="1">
			<title></title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<components>
		<component module_id="pages" component="photo" m_connection="" module="pages" is_controller="0" is_block="1" is_active="1" />
		<component module_id="pages" component="view" m_connection="pages.view" module="pages" is_controller="1" is_block="0" is_active="1" />
		<component module_id="pages" component="index" m_connection="pages.index" module="pages" is_controller="1" is_block="0" is_active="1" />
		<component module_id="pages" component="like" m_connection="" module="pages" is_controller="0" is_block="1" is_active="1" />
		<component module_id="pages" component="menu" m_connection="" module="pages" is_controller="0" is_block="1" is_active="1" />
		<component module_id="pages" component="index" m_connection="pages.index" module="pages" is_controller="1" is_block="0" is_active="1" />
		<component module_id="pages" component="category" m_connection="" module="pages" is_controller="0" is_block="1" is_active="1" />
		<component module_id="pages" component="profile" m_connection="" module="pages" is_controller="0" is_block="1" is_active="1" />
		<component module_id="pages" component="widget" m_connection="" module="pages" is_controller="0" is_block="1" is_active="1" />
	</components>
	<phrases>
		<phrase module_id="pages" version_id="3.0.0beta1" var_name="module_pages" added="1309870711">Pages</phrase>
		<phrase module_id="pages" version_id="3.0.0beta1" var_name="menu_pages_pages_fad58de7366495db4650cfefac2fcd61" added="1309937808">Pages</phrase>
		<phrase module_id="pages" version_id="3.0.0beta1" var_name="user_setting_max_upload_size_pages" added="1310024854"><![CDATA[Max file size for event photos in kilobits (kb).
(1000 kb = 1 mb)
For unlimited add "0" without quotes.]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta1" var_name="menu_pages_create_a_page_42a6b1a9f5d9d3cb02b85677b552fda0" added="1310037453">Create a Page</phrase>
		<phrase module_id="pages" version_id="3.0.0beta1" var_name="user_setting_can_moderate_pages" added="1311776645">Can moderate pages? This will allow a user to edit/delete/approve pages added by other users.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta1" var_name="user_setting_approve_pages" added="1311780223">Approve a new page before it is displayed publicly?</phrase>
		<phrase module_id="pages" version_id="3.0.0beta1" var_name="user_setting_points_pages" added="1311782221">Activity points received when creating a new page.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta1" var_name="admin_menu_add_category" added="1312875739">Add Category</phrase>
		<phrase module_id="pages" version_id="3.0.0beta1" var_name="admin_menu_manage_categories" added="1312875739">Manage Categories</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="user_setting_can_add_new_pages" added="1315737678">Can create new pages?</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="pages" added="1316525998">Pages</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="widgets" added="1316530442">Widgets</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="unable_to_find_the_page_you_are_trying_to_comment_on" added="1316530460">Unable to find the page you are trying to comment on.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="full_name_wrote_a_comment_on_your_page_title" added="1316530494"><![CDATA[{full_name} wrote a comment on your page "{title}".]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="full_name_wrote_a_comment_link" added="1316530579"><![CDATA[{full_name} wrote a comment on your page "<a href="{link}">{title}</a>".
To see the comment thread, follow the link below:
<a href="{link}">{link}</a>]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="page_name_not_allowed_please_select_another_name" added="1316530628">Page name not allowed. Please select another name.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="successfully_updated_your_pages_url" added="1316530638">Successfully updated your pages URL.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="url_updated" added="1316530648">URL Updated</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="successfully_registered_for_this_page" added="1316530667">Successfully registered for this page.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="moderation" added="1316530678">Moderation</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="login_as_a_page" added="1316530696">Login as a Page</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="pages_s_successfully_approved" added="1316530707">Pages(s) successfully approved.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="pages_s_successfully_deleted" added="1316530717">Pages(s) successfully deleted.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="page_has_been_approved" added="1316530735">Page has been approved.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="page_approved" added="1316530745">Page Approved</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="sub_categories" added="1316530764">Sub-Categories</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="categories" added="1316530772">Categories</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="members_total" added="1316530806">Members ({total})</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="likes" added="1316530831">Likes</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="successfully_updated_the_category" added="1316530873">Successfully updated the category.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="successfully_created_a_new_category" added="1316530894">Successfully created a new category.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="add_category" added="1316530904">Add Category</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="successfully_deleted_the_category" added="1316530922">Successfully deleted the category.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="manage_sub_categories" added="1316530937">Manage Sub-Categories</phrase>
		<phrase module_id="pages" version_id="3.0.0beta3" var_name="manage_categories" added="1316530950">Manage Categories</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="details" added="1316617701">Details</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="information" added="1316617709">Information</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="photo" added="1316617718">Photo</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="permissions" added="1316617725">Permissions</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="invite" added="1316617734">Invite</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="url" added="1316617744">URL</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="admins" added="1316617752">Admins</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="view_this_page" added="1316617776">View This Page</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="editing_page" added="1316625572">Editing Page</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="creating_a_page" added="1316625585">Creating a Page</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="page_successfully_deleted" added="1316625616">Page successfully deleted.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="search_pages" added="1316625648">Search Pages...</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="latest" added="1316625658">Latest</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="most_liked" added="1316625670">Most Liked</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="all_pages" added="1316625685">All Pages</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="my_pages" added="1316625695">My Pages</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="friends_pages" added="1316626008"><![CDATA[Friends' Pages]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="pending_pages" added="1316626019">Pending Pages</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="delete" added="1316626031">Delete</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="approve" added="1316626042">Approve</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="the_page_you_are_looking_for_cannot_be_found" added="1316626056">The page you are looking for cannot be found.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="unable_to_view_this_section_due_to_privacy_settings" added="1316626084">Unable to view this section due to privacy settings.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="in_the_page_link_title" added="1316626179"><![CDATA[In the page <a href="{link}">{title}</a>]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="liked_the_page_link_title_title" added="1316626262"><![CDATA[liked the page "<a href="{link}" title="{link_title}">{title}</a>".]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_invited_you_to_check_out_the_page_title" added="1316626340"><![CDATA[{users} invited you to check out the page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="full_name_liked_your_page_title" added="1316626489"><![CDATA[{full_name} liked your page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="full_name_liked_your_page" added="1316626575"><![CDATA[{full_name} liked your page "<a href="{link}">{title}</a>"
To view this page follow the link below:
<a href="{link}">{link}</a>]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="membership_accepted_to_title" added="1316626617"><![CDATA[Membership accepted to "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="your_membership_to_the_page_link" added="1316626679"><![CDATA[Your membership to the page "<a href="{link}">{title}</a>" has been accepted.
To view this page follow the link below:
<a href="{link}">{link}</a>]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="your_membership_has_been_accepted_to_join_the_page_title" added="1316626814"><![CDATA[Your membership has been accepted to join the page "{title}".]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_joined_gender_own_page_title" added="1316626982"><![CDATA[{users} joined {gender} own page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_joined_your_page_title" added="1316627022"><![CDATA[{users} joined your page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_joined_full_names_page_title" added="1316627075"><![CDATA[{users} joined <span class="drop_data_user">{full_name}'s</span> page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_liked_gender_own_page_title" added="1316627125"><![CDATA[{users} liked {gender} own page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_liked_your_page_title" added="1316627180"><![CDATA[{users} liked your page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_liked_full_names_page_title" added="1316627228"><![CDATA[{users} liked <span class="drop_data_user">{full_name}'s</span> page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="who_can_post_a_comment" added="1316627275">Who can post a comment?</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="who_can_view_browse_comments" added="1316627285">Who can view/browse comments?</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="unable_to_post_a_comment_on_this_item_due_to_privacy_settings" added="1316627297">Unable to post a comment on this item due to privacy settings.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="full_name_commented_on_a_comment_posted_on_the_page_title" added="1316627327"><![CDATA[{full_name} commented on a comment posted on the page "{title}".]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="full_name_commented_on_one_of_your_comments" added="1316627408"><![CDATA[{full_name} commented on one of your comments you posted on the page "<a href="{item_link}">{title}</a>".
To see the comment thread, follow the link below:
<a href="{link}">{link}</a>]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="full_name_commented_on_one_of_gender_page_comments" added="1316627479">{full_name} commented on one of {gender} page comments.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="full_name_commented_on_one_of_other_full_name_s_page_comments" added="1316627535"><![CDATA[{full_name} commented on one of {other_full_name}'s page comments.]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="full_name_comment_on_one_of_gender" added="1316627681"><![CDATA[{full_name} commented on one of {gender} own comments on the page "<a href="{item_link}">{title}</a>".
To see the comment thread, follow the link below:
<a href="{link}">{link}</a>]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="full_name_commented_on_one_of_other_full_name" added="1316627849"><![CDATA[{full_name} commented on one of {other_full_name}'s comments on the page "<a href="{item_link}">{title}</a>".
To see the comment thread, follow the link below:
<a href="{link}">{link}</a>]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_commented_on_full_names_page" added="1316712977"><![CDATA[{users} commented on <span class="drop_data_user">{full_name}'s</span> page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_commented_on_gender_own_page_title" added="1316713081"><![CDATA[{users} commented on {gender} own page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_commented_on_your_page_title" added="1316713123"><![CDATA[{users} commented on your page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_commented_on_span_class_drop_data_user_full_name_s_span_comment_on_the_page_title" added="1316713344"><![CDATA[{users} commented on <span class="drop_data_user">{full_name}'s</span> comment on the page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_commented_on_gender_own_comment_on_the_page_title" added="1316713395"><![CDATA[{users} commented on {gender} own comment on the page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_commented_on_one_of_your_comments_on_the_page_title" added="1316713436"><![CDATA[{users} commented on one of your comments on the page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_commented_on_one_of_full_name" added="1316713481"><![CDATA[{users} commented on one of <span class="drop_data_user">{full_name}'s</span> comments on the page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="page" added="1316713666">Page</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="unable_to_find_the_page_you_are_looking_for" added="1316713720">Unable to find the page you are looking for.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="pending_memberships" added="1316713736">Pending Memberships</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="wall" added="1316713746">Wall</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="info" added="1316713755">Info</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="unable_to_find_the_page_you_are_trying_to_edit" added="1316713777">Unable to find the page you are trying to edit.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="you_are_unable_to_edit_this_page" added="1316713789">You are unable to edit this page.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="unable_to_delete_this_widget" added="1316713824">Unable to delete this widget.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="unable_to_add_a_widget_to_this_page" added="1316713845">Unable to add a widget to this page.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="provide_a_title_for_your_widget" added="1316713858">Provide a title for your widget.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="provide_content_for_your_widget" added="1316713884">Provide content for your widget.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="provide_a_menu_title_for_your_widget" added="1316713894">Provide a menu title for your widget.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="provide_a_url_title_for_your_widget" added="1316713909">Provide a URL title for your widget.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="you_cannot_use_this_url_for_your_widget" added="1316713922">You cannot use this URL for your widget.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="page_name_cannot_be_empty" added="1316713946">Page name cannot be empty.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="full_name_invited_you_to_the_page_title" added="1316713980"><![CDATA[{full_name} invited you to the page "{title}".]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="to_view_this_page_click_the_link_below_a_href_link_link_a" added="1316714024"><![CDATA[To view this page click the link below:
<a href="{link}">{link}</a>]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="full_name_sent_you_a_page_invitation" added="1316714062">{full_name} sent you a page invitation.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="unable_to_find_the_page_you_are_trying_to_login_to" added="1316714098">Unable to find the page you are trying to login to.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="unable_to_log_in_as_this_page" added="1316714108">Unable to log in as this page.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="unable_to_find_the_page_you_are_trying_to_delete" added="1316714120">Unable to find the page you are trying to delete.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="you_are_unable_to_delete_this_page" added="1316714132">You are unable to delete this page.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="unable_to_find_the_page_you_are_trying_to_approve" added="1316714149">Unable to find the page you are trying to approve.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="page_title_approved" added="1316714174"><![CDATA[Page "{title}" approved!]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="your_page_title_has_been_approved" added="1316714245"><![CDATA[Your page "{title}" has been approved. To view this page follow the link below:
<a href="{link}">{link}</a>]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_commented_on_the_page_title" added="1317032878"><![CDATA[{users} commented on the page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_commented_on_full_name_comment" added="1317900448"><![CDATA[{users} commented on <span class="drop_data_user">{full_name}'s</span> comment on the page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_commented_on_gender_own_comment" added="1317900524"><![CDATA[{users} commented on {gender} own comment on the page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_commented_on_one_of_your_comments" added="1317900589"><![CDATA[{users} commented on one of your comments on the page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta4" var_name="users_commented_on_one_of_full_name_comments" added="1317900666"><![CDATA[{users} commented on one of <span class="drop_data_user">{full_name}'s</span> comments on the page "{title}"]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="invitations_sent_out" added="1319112534">Invitations sent out.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="unable_to_find_the_page" added="1319112551">Unable to find the page.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="unable_to_moderate_this_page" added="1319112561">Unable to moderate this page.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="go_to_app" added="1319112599">Go to App</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="create_a_page" added="1319112605">Create a Page</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="join" added="1319112623">Join</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="like" added="1319112629">Like</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="no_members_yet" added="1319112645">No members yet.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="person_likes_this" added="1319112678">person likes this</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="people_like_this" added="1319112686">people like this</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="manage" added="1319112696">Manage</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="are_you_sure" added="1319112714">Are you sure?</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="select_switch_below_to_use_this_site_as_a_page" added="1319112741"><![CDATA[Select "Switch" below to use this site as a Page and interact with others as that Page.]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="switch" added="1319112752">Switch</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="you_currently_do_not_have_any_pages" added="1319112778"><![CDATA[You currently do not have any pages. Create a new page <a href="{link}">here</a>.]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="edit_page" added="1319112827">Edit Page</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="remove_membership" added="1319112839">Remove Membership</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="unlike" added="1319112846">Unlike</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="more" added="1319112897">More</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="category_details" added="1319112915">Category Details</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="parent_category" added="1319112923">Parent Category</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="none" added="1319112929">None</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="name" added="1319112939">Name</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="is_a_group" added="1319112947">Is a group?</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="yes" added="1319112956">Yes</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="no" added="1319112961">No</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="if_a_page_is_considered_a_group_it_will_require_users_to_become_members" added="1319112975">If a page is considered a group it will require users to become members of that page, instead of just allowing users to Like a page.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="submit" added="1319112982">Submit</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="active" added="1319113002">Active</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="edit" added="1319113016">Edit</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="manage_sub_categories_total" added="1319113029">Manage Sub-Categories ({total})</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="deactivate" added="1319113064">Deactivate</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="activate" added="1319113071">Activate</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="category" added="1319113085">Category</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="app" added="1319113090">App</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="landing_page" added="1319113115">Landing Page</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="update" added="1319113124">Update</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="vanity_url" added="1319113132">Vanity URL</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="check_url" added="1319113147">Check URL</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="click_here_to_change_this_photo" added="1319113175"><![CDATA[Click <a href="#" id="js_pages_add_change_photo">here</a> to change this photo.]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="upload_photo" added="1319113186">Upload Photo</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="page_privacy" added="1319113202">Page Privacy</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="control_who_can_see_this_page" added="1319113214">Control who can see this page.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="page_registration_method" added="1319113231">Page Registration Method</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="anyone" added="1319113237">Anyone</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="approval_first" added="1319113248">Approval First</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="members_only" added="1319113255">Members Only</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="admins_only" added="1319113263">Admins Only</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="remove" added="1319113282">Remove</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="search_friends_by_their_name" added="1319113296">Search friends by their name...</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="create_new_widget" added="1319113311">Create New Widget</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="actions" added="1319113335">Actions</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="connect_with_friends_associates_amp_fans" added="1319113360"><![CDATA[Connect with friends, associates &amp; fans.]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="choose_a_category" added="1319113390">Choose a category</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="get_started" added="1319113406">Get Started</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="invite_only" added="1319114205">Invite Only</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="note_that_pages_displayed_here_are_pages_created_by_the_page" added="1319114265">Note that Pages displayed here are pages created by the Page ({global_full_name}) and not by the parent user ({profile_full_name}).</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="moderate" added="1319114337">Moderate</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="total_members" added="1319114360">{total} members</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="1_member" added="1319114380">1 member</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="no_members" added="1319114389">No members</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="no_pages_found" added="1319114396">No pages found.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="this_page_is_pending_an_admins_approval_before_it_can_be_displayed_publicly" added="1319114407">This page is pending an Admins approval before it can be displayed publicly.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="you_have_been_invited_to_join_this_community" added="1319114443">You have been invited to join this community.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="due_to_privacy_settings_this_page_is_not_visible" added="1319114456">Due to privacy settings this page is not visible.</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="this_page_is_also_invite_only" added="1319114467"><![CDATA[This page is also "Invite Only".]]></phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="title" added="1319114483">Title</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="is_a_block" added="1319114491">Is a block?</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="menu_title" added="1319114510">Menu Title</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="content" added="1319114526">Content</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="successfully_moderated_user_s" added="1319183922">Successfully moderated user(s).</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="events" added="1319187962">Events</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="music" added="1319187976">Music</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="photos" added="1319187984">Photos</phrase>
		<phrase module_id="pages" version_id="3.0.0beta5" var_name="discussions" added="1319187992">Discussions</phrase>
	</phrases>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="pages" type="integer" admin="500" user="500" guest="0" staff="500" module="pages" ordering="0">max_upload_size_pages</setting>
		<setting is_admin_setting="0" module_id="pages" type="boolean" admin="1" user="0" guest="0" staff="1" module="pages" ordering="0">can_moderate_pages</setting>
		<setting is_admin_setting="0" module_id="pages" type="boolean" admin="0" user="0" guest="0" staff="0" module="pages" ordering="0">approve_pages</setting>
		<setting is_admin_setting="0" module_id="pages" type="integer" admin="1" user="1" guest="0" staff="1" module="pages" ordering="0">points_pages</setting>
		<setting is_admin_setting="0" module_id="pages" type="boolean" admin="1" user="1" guest="0" staff="1" module="pages" ordering="0">can_add_new_pages</setting>
	</user_group_settings>
	<tables><![CDATA[a:14:{s:12:"phpfox_pages";a:3:{s:7:"COLUMNS";a:15:{s:7:"page_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:6:"app_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"view_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"category_id";a:4:{i:0;s:4:"UINT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"reg_method";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:12:"landing_page";a:4:{i:0;s:8:"VCHAR:50";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"image_path";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:15:"image_server_id";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_comment";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"privacy";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"page_id";s:4:"KEYS";a:8:{s:11:"category_id";a:2:{i:0;s:5:"INDEX";i:1;s:11:"category_id";}s:9:"view_id_2";a:2:{i:0;s:5:"INDEX";i:1;s:7:"view_id";}s:6:"app_id";a:2:{i:0;s:5:"INDEX";i:1;s:6:"app_id";}s:8:"app_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:6:"app_id";i:1;s:7:"view_id";i:2;s:7:"privacy";}}s:8:"app_id_3";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:6:"app_id";i:1;s:7:"view_id";i:2;s:7:"user_id";}}s:8:"app_id_4";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:6:"app_id";i:1;s:7:"view_id";}}s:7:"view_id";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"view_id";i:1;s:5:"title";i:2;s:7:"privacy";}}s:7:"page_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"page_id";i:1;s:7:"view_id";}}}}s:18:"phpfox_pages_admin";a:2:{s:7:"COLUMNS";a:2:{s:7:"page_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:3:{s:9:"page_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"page_id";i:1;s:7:"user_id";}}s:7:"page_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"page_id";}s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}}}s:21:"phpfox_pages_category";a:3:{s:7:"COLUMNS";a:6:{s:11:"category_id";a:4:{i:0;s:4:"UINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"name";a:4:{i:0;s:9:"VCHAR:200";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"page_type";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:11:"category_id";s:4:"KEYS";a:2:{s:7:"type_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"type_id";i:1;s:9:"is_active";}}s:11:"category_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:11:"category_id";i:1;s:9:"is_active";}}}}s:17:"phpfox_pages_feed";a:3:{s:7:"COLUMNS";a:8:{s:7:"feed_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"privacy";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"privacy_comment";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:14:"parent_user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"item_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"feed_id";s:4:"KEYS";a:1:{s:14:"parent_user_id";a:2:{i:0;s:5:"INDEX";i:1;s:14:"parent_user_id";}}}s:25:"phpfox_pages_feed_comment";a:3:{s:7:"COLUMNS";a:9:{s:15:"feed_comment_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:14:"parent_user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"privacy";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"privacy_comment";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"content";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_comment";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:15:"feed_comment_id";s:4:"KEYS";a:1:{s:14:"parent_user_id";a:2:{i:0;s:5:"INDEX";i:1;s:14:"parent_user_id";}}}s:19:"phpfox_pages_invite";a:3:{s:7:"COLUMNS";a:8:{s:9:"invite_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"page_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"visited_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"invited_user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"invited_email";a:4:{i:0;s:9:"VCHAR:100";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:9:"invite_id";s:4:"KEYS";a:6:{s:10:"listing_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"page_id";}s:12:"listing_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"page_id";i:1;s:15:"invited_user_id";}}s:15:"invited_user_id";a:2:{i:0;s:5:"INDEX";i:1;s:15:"invited_user_id";}s:12:"listing_id_3";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"page_id";i:1;s:10:"visited_id";}}s:12:"listing_id_4";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"page_id";i:1;s:10:"visited_id";i:2;s:15:"invited_user_id";}}s:10:"visited_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:10:"visited_id";i:1;s:15:"invited_user_id";}}}}s:18:"phpfox_pages_login";a:3:{s:7:"COLUMNS";a:4:{s:8:"login_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"page_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:8:"login_id";s:4:"KEYS";a:2:{s:8:"login_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:8:"login_id";i:1;s:7:"page_id";}}s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}}}s:17:"phpfox_pages_perm";a:2:{s:7:"COLUMNS";a:3:{s:7:"page_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"var_name";a:4:{i:0;s:9:"VCHAR:150";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"var_value";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:1:{s:7:"page_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"page_id";}}}s:19:"phpfox_pages_signup";a:3:{s:7:"COLUMNS";a:4:{s:9:"signup_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"page_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:9:"signup_id";s:4:"KEYS";a:2:{s:7:"page_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"page_id";}s:9:"page_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"page_id";i:1;s:7:"user_id";}}}}s:17:"phpfox_pages_text";a:2:{s:7:"COLUMNS";a:3:{s:7:"page_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"text";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"text_parsed";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:4:"KEYS";a:1:{s:7:"page_id";a:2:{i:0;s:6:"UNIQUE";i:1;s:7:"page_id";}}}s:17:"phpfox_pages_type";a:3:{s:7:"COLUMNS";a:5:{s:7:"type_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}s:4:"name";a:4:{i:0;s:9:"VCHAR:200";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"type_id";s:4:"KEYS";a:1:{s:9:"is_active";a:2:{i:0;s:5:"INDEX";i:1;s:9:"is_active";}}}s:16:"phpfox_pages_url";a:2:{s:7:"COLUMNS";a:2:{s:10:"vanity_url";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"page_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:2:{s:7:"page_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"page_id";}s:10:"vanity_url";a:2:{i:0;s:5:"INDEX";i:1;s:10:"vanity_url";}}}s:19:"phpfox_pages_widget";a:3:{s:7:"COLUMNS";a:8:{s:9:"widget_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"page_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:9:"VCHAR:200";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"is_block";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"menu_title";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"url_title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:9:"widget_id";s:4:"KEYS";a:1:{s:7:"page_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"page_id";}}}s:24:"phpfox_pages_widget_text";a:2:{s:7:"COLUMNS";a:3:{s:9:"widget_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"text";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"text_parsed";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:4:"KEYS";a:1:{s:9:"widget_id";a:2:{i:0;s:5:"INDEX";i:1;s:9:"widget_id";}}}}]]></tables>
	<install><![CDATA[
		$aPageCategories = array (
		  0 => 
		  array (
			'name' => 'Entertainment',
			'categories' => 
			array (
			  0 => 
			  array (
				'name' => 'Album',
				'page_type' => '0',
			  ),
			  1 => 
			  array (
				'name' => 'Amateur Sports Team',
				'page_type' => '0',
			  ),
			  2 => 
			  array (
				'name' => 'Book',
				'page_type' => '0',
			  ),
			  3 => 
			  array (
				'name' => 'Book Store',
				'page_type' => '0',
			  ),
			  4 => 
			  array (
				'name' => 'Concert Tour',
				'page_type' => '0',
			  ),
			  5 => 
			  array (
				'name' => 'Concert Venue',
				'page_type' => '0',
			  ),
			  6 => 
			  array (
				'name' => 'Fictional Character',
				'page_type' => '0',
			  ),
			  7 => 
			  array (
				'name' => 'Library',
				'page_type' => '0',
			  ),
			  8 => 
			  array (
				'name' => 'Magazine',
				'page_type' => '0',
			  ),
			  9 => 
			  array (
				'name' => 'Movie',
				'page_type' => '0',
			  ),
			  10 => 
			  array (
				'name' => 'Movie Theater',
				'page_type' => '0',
			  ),
			  11 => 
			  array (
				'name' => 'Music Award',
				'page_type' => '0',
			  ),
			  12 => 
			  array (
				'name' => 'Music Chart',
				'page_type' => '0',
			  ),
			  13 => 
			  array (
				'name' => 'Music Video',
				'page_type' => '0',
			  ),
			  14 => 
			  array (
				'name' => 'Musical Instrument',
				'page_type' => '0',
			  ),
			  15 => 
			  array (
				'name' => 'Playlist',
				'page_type' => '0',
			  ),
			  16 => 
			  array (
				'name' => 'Professional Sports Team',
				'page_type' => '0',
			  ),
			  17 => 
			  array (
				'name' => 'Radio Station',
				'page_type' => '0',
			  ),
			  18 => 
			  array (
				'name' => 'Record Label',
				'page_type' => '0',
			  ),
			  19 => 
			  array (
				'name' => 'School Sports Team',
				'page_type' => '0',
			  ),
			  20 => 
			  array (
				'name' => 'Song',
				'page_type' => '0',
			  ),
			  21 => 
			  array (
				'name' => 'Sports League',
				'page_type' => '0',
			  ),
			  22 => 
			  array (
				'name' => 'Sports Venue',
				'page_type' => '0',
			  ),
			  23 => 
			  array (
				'name' => 'Studio',
				'page_type' => '0',
			  ),
			  24 => 
			  array (
				'name' => 'TV Channel',
				'page_type' => '0',
			  ),
			  25 => 
			  array (
				'name' => 'TV Network',
				'page_type' => '0',
			  ),
			  26 => 
			  array (
				'name' => 'TV Show',
				'page_type' => '0',
			  ),
			  27 => 
			  array (
				'name' => 'TV/Movie Award',
				'page_type' => '0',
			  ),
			),
		  ),
		  1 => 
		  array (
			'name' => 'Brand or Product',
			'categories' => 
			array (
			  0 => 
			  array (
				'name' => 'Appliances',
				'page_type' => '0',
			  ),
			  1 => 
			  array (
				'name' => 'Baby Goods/Kids Goods',
				'page_type' => '0',
			  ),
			  2 => 
			  array (
				'name' => 'Bags/Luggage',
				'page_type' => '0',
			  ),
			  3 => 
			  array (
				'name' => 'Building Materials',
				'page_type' => '0',
			  ),
			  4 => 
			  array (
				'name' => 'Camera/Photo',
				'page_type' => '0',
			  ),
			  5 => 
			  array (
				'name' => 'Cars',
				'page_type' => '0',
			  ),
			  6 => 
			  array (
				'name' => 'Clothing',
				'page_type' => '0',
			  ),
			  7 => 
			  array (
				'name' => 'Commercial Equipment',
				'page_type' => '0',
			  ),
			  8 => 
			  array (
				'name' => 'Computers',
				'page_type' => '0',
			  ),
			  9 => 
			  array (
				'name' => 'Drugs',
				'page_type' => '0',
			  ),
			  10 => 
			  array (
				'name' => 'Electronics',
				'page_type' => '0',
			  ),
			  11 => 
			  array (
				'name' => 'Food/Beverages',
				'page_type' => '0',
			  ),
			  12 => 
			  array (
				'name' => 'Furniture',
				'page_type' => '0',
			  ),
			  13 => 
			  array (
				'name' => 'Games/Toys',
				'page_type' => '0',
			  ),
			  14 => 
			  array (
				'name' => 'Health/Beauty',
				'page_type' => '0',
			  ),
			  15 => 
			  array (
				'name' => 'Home Decor',
				'page_type' => '0',
			  ),
			  16 => 
			  array (
				'name' => 'Household Supplies',
				'page_type' => '0',
			  ),
			  17 => 
			  array (
				'name' => 'Jewelry/Watches',
				'page_type' => '0',
			  ),
			  18 => 
			  array (
				'name' => 'Kitchen/Cooking',
				'page_type' => '0',
			  ),
			  19 => 
			  array (
				'name' => 'Movies/Music',
				'page_type' => '0',
			  ),
			  20 => 
			  array (
				'name' => 'Musical Instrument',
				'page_type' => '0',
			  ),
			  21 => 
			  array (
				'name' => 'Office Supplies',
				'page_type' => '0',
			  ),
			  22 => 
			  array (
				'name' => 'Outdoor Gear/Sporting Goods',
				'page_type' => '0',
			  ),
			  23 => 
			  array (
				'name' => 'Patio/Garden',
				'page_type' => '0',
			  ),
			  24 => 
			  array (
				'name' => 'Pet Supplies',
				'page_type' => '0',
			  ),
			  25 => 
			  array (
				'name' => 'Product/Service',
				'page_type' => '0',
			  ),
			  26 => 
			  array (
				'name' => 'Software',
				'page_type' => '0',
			  ),
			  27 => 
			  array (
				'name' => 'Tools/Equipment',
				'page_type' => '0',
			  ),
			  28 => 
			  array (
				'name' => 'Vitamins/Supplements',
				'page_type' => '0',
			  ),
			  29 => 
			  array (
				'name' => 'Website',
				'page_type' => '0',
			  ),
			  30 => 
			  array (
				'name' => 'Wine/Spirits',
				'page_type' => '0',
			  ),
			),
		  ),
		  2 => 
		  array (
			'name' => 'Group or Community',
			'categories' => 
			array (
			  0 => 
			  array (
				'name' => 'Business',
				'page_type' => '1',
			  ),
			  1 => 
			  array (
				'name' => 'Common Interest',
				'page_type' => '1',
			  ),
			  2 => 
			  array (
				'name' => 'Entertainment & Arts ',
				'page_type' => '1',
			  ),
			  3 => 
			  array (
				'name' => 'Geography',
				'page_type' => '1',
			  ),
			  4 => 
			  array (
				'name' => 'Internet & Technology',
				'page_type' => '1',
			  ),
			  5 => 
			  array (
				'name' => 'Just for Fun',
				'page_type' => '1',
			  ),
			  6 => 
			  array (
				'name' => 'Music',
				'page_type' => '1',
			  ),
			  7 => 
			  array (
				'name' => 'Organisations',
				'page_type' => '1',
			  ),
			  8 => 
			  array (
				'name' => 'Sports & Recreation',
				'page_type' => '1',
			  ),
			  9 => 
			  array (
				'name' => 'Student Groups',
				'page_type' => '1',
			  ),
			),
		  ),
		  3 => 
		  array (
			'name' => 'Local Business or Place',
			'categories' => 
			array (
			  0 => 
			  array (
				'name' => 'Airport',
				'page_type' => '0',
			  ),
			  1 => 
			  array (
				'name' => 'Arts/Entertainment/Nightlife',
				'page_type' => '0',
			  ),
			  2 => 
			  array (
				'name' => 'Attractions/Things to Do',
				'page_type' => '0',
			  ),
			  3 => 
			  array (
				'name' => 'Automotive',
				'page_type' => '0',
			  ),
			  4 => 
			  array (
				'name' => 'Bank/Financial Services',
				'page_type' => '0',
			  ),
			  5 => 
			  array (
				'name' => 'Bar',
				'page_type' => '0',
			  ),
			  6 => 
			  array (
				'name' => 'Book Store',
				'page_type' => '0',
			  ),
			  7 => 
			  array (
				'name' => 'Business Services',
				'page_type' => '0',
			  ),
			  8 => 
			  array (
				'name' => 'Church/Religious Organization',
				'page_type' => '0',
			  ),
			  9 => 
			  array (
				'name' => 'Club',
				'page_type' => '0',
			  ),
			  10 => 
			  array (
				'name' => 'Community/Government',
				'page_type' => '0',
			  ),
			  11 => 
			  array (
				'name' => 'Concert Venue',
				'page_type' => '0',
			  ),
			  12 => 
			  array (
				'name' => 'Education',
				'page_type' => '0',
			  ),
			  13 => 
			  array (
				'name' => 'Event Planning/Event Services',
				'page_type' => '0',
			  ),
			  14 => 
			  array (
				'name' => 'Food/Grocery',
				'page_type' => '0',
			  ),
			  15 => 
			  array (
				'name' => 'Health/Medical/Pharmacy',
				'page_type' => '0',
			  ),
			  16 => 
			  array (
				'name' => 'Home Improvement',
				'page_type' => '0',
			  ),
			  17 => 
			  array (
				'name' => 'Hospital/Clinic',
				'page_type' => '0',
			  ),
			  18 => 
			  array (
				'name' => 'Hotel',
				'page_type' => '0',
			  ),
			  19 => 
			  array (
				'name' => 'Landmark',
				'page_type' => '0',
			  ),
			  20 => 
			  array (
				'name' => 'Library',
				'page_type' => '0',
			  ),
			  21 => 
			  array (
				'name' => 'Local Business',
				'page_type' => '0',
			  ),
			  22 => 
			  array (
				'name' => 'Movie Theater',
				'page_type' => '0',
			  ),
			  23 => 
			  array (
				'name' => 'Museum/Art Gallery',
				'page_type' => '0',
			  ),
			  24 => 
			  array (
				'name' => 'Pet Services',
				'page_type' => '0',
			  ),
			  25 => 
			  array (
				'name' => 'Professional Services',
				'page_type' => '0',
			  ),
			  26 => 
			  array (
				'name' => 'Public Places',
				'page_type' => '0',
			  ),
			  27 => 
			  array (
				'name' => 'Real Estate',
				'page_type' => '0',
			  ),
			  28 => 
			  array (
				'name' => 'Restaurant/Cafe',
				'page_type' => '0',
			  ),
			  29 => 
			  array (
				'name' => 'School',
				'page_type' => '0',
			  ),
			  30 => 
			  array (
				'name' => 'Shopping/Retail',
				'page_type' => '0',
			  ),
			  31 => 
			  array (
				'name' => 'Spas/Beauty/Personal Care',
				'page_type' => '0',
			  ),
			  32 => 
			  array (
				'name' => 'Sports Venue',
				'page_type' => '0',
			  ),
			  33 => 
			  array (
				'name' => 'Sports/Recreation/Activities',
				'page_type' => '0',
			  ),
			  34 => 
			  array (
				'name' => 'Tours/Sightseeing',
				'page_type' => '0',
			  ),
			  35 => 
			  array (
				'name' => 'Transit Stop',
				'page_type' => '0',
			  ),
			  36 => 
			  array (
				'name' => 'Transportation',
				'page_type' => '0',
			  ),
			  37 => 
			  array (
				'name' => 'University',
				'page_type' => '0',
			  ),
			),
		  ),
		  4 => 
		  array (
			'name' => 'Company, Organization, or Institution',
			'categories' => 
			array (
			  0 => 
			  array (
				'name' => 'Aerospace/Defense',
				'page_type' => '0',
			  ),
			  1 => 
			  array (
				'name' => 'Automobiles and Parts',
				'page_type' => '0',
			  ),
			  2 => 
			  array (
				'name' => 'Bank/Financial Institution',
				'page_type' => '0',
			  ),
			  3 => 
			  array (
				'name' => 'Biotechnology',
				'page_type' => '0',
			  ),
			  4 => 
			  array (
				'name' => 'Cause',
				'page_type' => '0',
			  ),
			  5 => 
			  array (
				'name' => 'Chemicals',
				'page_type' => '0',
			  ),
			  6 => 
			  array (
				'name' => 'Church/Religious Organization',
				'page_type' => '0',
			  ),
			  7 => 
			  array (
				'name' => 'Community Organization',
				'page_type' => '0',
			  ),
			  8 => 
			  array (
				'name' => 'Company',
				'page_type' => '0',
			  ),
			  9 => 
			  array (
				'name' => 'Computers/Technology',
				'page_type' => '0',
			  ),
			  10 => 
			  array (
				'name' => 'Consulting/Business Services',
				'page_type' => '0',
			  ),
			  11 => 
			  array (
				'name' => 'Education',
				'page_type' => '0',
			  ),
			  12 => 
			  array (
				'name' => 'Energy/Utility',
				'page_type' => '0',
			  ),
			  13 => 
			  array (
				'name' => 'Engineering/Construction',
				'page_type' => '0',
			  ),
			  14 => 
			  array (
				'name' => 'Farming/Agriculture',
				'page_type' => '0',
			  ),
			  15 => 
			  array (
				'name' => 'Food/Beverages',
				'page_type' => '0',
			  ),
			  16 => 
			  array (
				'name' => 'Government Organization',
				'page_type' => '0',
			  ),
			  17 => 
			  array (
				'name' => 'Health/Beauty',
				'page_type' => '0',
			  ),
			  18 => 
			  array (
				'name' => 'Health/Medical/Pharmaceuticals',
				'page_type' => '0',
			  ),
			  19 => 
			  array (
				'name' => 'Industrials',
				'page_type' => '0',
			  ),
			  20 => 
			  array (
				'name' => 'Insurance Company',
				'page_type' => '0',
			  ),
			  21 => 
			  array (
				'name' => 'Internet/Software',
				'page_type' => '0',
			  ),
			  22 => 
			  array (
				'name' => 'Legal/Law',
				'page_type' => '0',
			  ),
			  23 => 
			  array (
				'name' => 'Media/News/Publishing',
				'page_type' => '0',
			  ),
			  24 => 
			  array (
				'name' => 'Mining/Materials',
				'page_type' => '0',
			  ),
			  25 => 
			  array (
				'name' => 'Non-Governmental Organization (NGO)',
				'page_type' => '0',
			  ),
			  26 => 
			  array (
				'name' => 'Non-Profit Organization',
				'page_type' => '0',
			  ),
			  27 => 
			  array (
				'name' => 'Organization',
				'page_type' => '0',
			  ),
			  28 => 
			  array (
				'name' => 'Political Organization',
				'page_type' => '0',
			  ),
			  29 => 
			  array (
				'name' => 'Political Party',
				'page_type' => '0',
			  ),
			  30 => 
			  array (
				'name' => 'Retail and Consumer Merchandise',
				'page_type' => '0',
			  ),
			  31 => 
			  array (
				'name' => 'School',
				'page_type' => '0',
			  ),
			  32 => 
			  array (
				'name' => 'Small Business',
				'page_type' => '0',
			  ),
			  33 => 
			  array (
				'name' => 'Telecommunication',
				'page_type' => '0',
			  ),
			  34 => 
			  array (
				'name' => 'Transport/Freight',
				'page_type' => '0',
			  ),
			  35 => 
			  array (
				'name' => 'Travel/Leisure',
				'page_type' => '0',
			  ),
			  36 => 
			  array (
				'name' => 'University',
				'page_type' => '0',
			  ),
			),
		  ),
		  5 => 
		  array (
			'name' => 'Artist, Band or Public Figure',
			'categories' => 
			array (
			  0 => 
			  array (
				'name' => 'Actor/Director',
				'page_type' => '0',
			  ),
			  1 => 
			  array (
				'name' => 'Artist',
				'page_type' => '0',
			  ),
			  2 => 
			  array (
				'name' => 'Athlete',
				'page_type' => '0',
			  ),
			  3 => 
			  array (
				'name' => 'Author',
				'page_type' => '0',
			  ),
			  4 => 
			  array (
				'name' => 'Business Person',
				'page_type' => '0',
			  ),
			  5 => 
			  array (
				'name' => 'Chef',
				'page_type' => '0',
			  ),
			  6 => 
			  array (
				'name' => 'Coach',
				'page_type' => '0',
			  ),
			  7 => 
			  array (
				'name' => 'Comedian',
				'page_type' => '0',
			  ),
			  8 => 
			  array (
				'name' => 'Dancer',
				'page_type' => '0',
			  ),
			  9 => 
			  array (
				'name' => 'Doctor',
				'page_type' => '0',
			  ),
			  10 => 
			  array (
				'name' => 'Editor',
				'page_type' => '0',
			  ),
			  11 => 
			  array (
				'name' => 'Entertainer',
				'page_type' => '0',
			  ),
			  12 => 
			  array (
				'name' => 'Fictional Character',
				'page_type' => '0',
			  ),
			  13 => 
			  array (
				'name' => 'Government Official',
				'page_type' => '0',
			  ),
			  14 => 
			  array (
				'name' => 'Journalist',
				'page_type' => '0',
			  ),
			  15 => 
			  array (
				'name' => 'Lawyer',
				'page_type' => '0',
			  ),
			  16 => 
			  array (
				'name' => 'Monarch',
				'page_type' => '0',
			  ),
			  17 => 
			  array (
				'name' => 'Musician/Band',
				'page_type' => '0',
			  ),
			  18 => 
			  array (
				'name' => 'News Personality',
				'page_type' => '0',
			  ),
			  19 => 
			  array (
				'name' => 'Politician',
				'page_type' => '0',
			  ),
			  20 => 
			  array (
				'name' => 'Producer',
				'page_type' => '0',
			  ),
			  21 => 
			  array (
				'name' => 'Public Figure',
				'page_type' => '0',
			  ),
			  22 => 
			  array (
				'name' => 'Teacher',
				'page_type' => '0',
			  ),
			  23 => 
			  array (
				'name' => 'Writer',
				'page_type' => '0',
			  ),
			),
		  ),
		);
		
		$iCnt = 0;
		foreach ($aPageCategories as $aCategory)
		{
			$iCnt++;
			$iInsertId = $this->database()->insert(Phpfox::getT('pages_type'), array(
					'is_active' => '1',
					'name' => $aCategory['name'],
					'time_stamp' => PHPFOX_TIME,
					'ordering' => $iCnt
				)
			);
			
			$iSubCnt = 0;
			foreach ($aCategory['categories'] as $aSub)
			{
				$iSubCnt++;
				$this->database()->insert(Phpfox::getT('pages_category'), array(
						'type_id' => $iInsertId,
						'is_active' => '1',
						'name' => $aSub['name'],
						'page_type' => $aSub['page_type'],
						'ordering' => $iSubCnt
					)
				);
			}			
		}
	]]></install>
</module>