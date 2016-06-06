<upgrade>
	<settings>
		<setting>
			<group />
			<module_id>user</module_id>
			<is_hidden>0</is_hidden>
			<type>integer</type>
			<var_name>date_of_birth_start</var_name>
			<phrase_var_name>setting_date_of_birth_start</phrase_var_name>
			<ordering>1</ordering>
			<version_id>2.0.0rc11</version_id>
			<value>1900</value>
		</setting>
		<setting>
			<group />
			<module_id>user</module_id>
			<is_hidden>0</is_hidden>
			<type>integer</type>
			<var_name>date_of_birth_end</var_name>
			<phrase_var_name>setting_date_of_birth_end</phrase_var_name>
			<ordering>1</ordering>
			<version_id>2.0.0rc11</version_id>
			<value>1997</value>
		</setting>
	</settings>
	<phpfox_update_settings>
		<setting>
			<group>registration</group>
			<module_id>user</module_id>
			<is_hidden>0</is_hidden>
			<type>array</type>
			<var_name>usernames_to_suggest</var_name>
			<phrase_var_name>setting_usernames_to_suggest</phrase_var_name>
			<ordering>2</ordering>
			<version_id>2.0.0beta3</version_id>
			<value><![CDATA[s:34:"array('user', 'member', 'friend');";]]></value>
		</setting>
		<setting>
			<group>registration</group>
			<module_id>user</module_id>
			<is_hidden>0</is_hidden>
			<type>integer</type>
			<var_name>how_many_usernames_to_suggest</var_name>
			<phrase_var_name>setting_how_many_usernames_to_suggest</phrase_var_name>
			<ordering>3</ordering>
			<version_id>2.0.0beta3</version_id>
			<value>4</value>
		</setting>
		<setting>
			<group>registration</group>
			<module_id>user</module_id>
			<is_hidden>0</is_hidden>
			<type>boolean</type>
			<var_name>captcha_on_signup</var_name>
			<phrase_var_name>setting_captcha_on_signup</phrase_var_name>
			<ordering>3</ordering>
			<version_id>2.0.0alpha1</version_id>
			<value>1</value>
		</setting>
		<setting>
			<group>registration</group>
			<module_id>user</module_id>
			<is_hidden>0</is_hidden>
			<type>array</type>
			<var_name>registration_steps</var_name>
			<phrase_var_name>setting_registration_steps</phrase_var_name>
			<ordering>1</ordering>
			<version_id>2.0.0alpha2</version_id>
			<value />
		</setting>
		<setting>
			<group>registration</group>
			<module_id>user</module_id>
			<is_hidden>0</is_hidden>
			<type>boolean</type>
			<var_name>multi_step_registration_form</var_name>
			<phrase_var_name>setting_multi_step_registration_form</phrase_var_name>
			<ordering>1</ordering>
			<version_id>2.0.0alpha2</version_id>
			<value>1</value>
		</setting>
		<setting>
			<group>registration</group>
			<module_id>user</module_id>
			<is_hidden>0</is_hidden>
			<type>boolean</type>
			<var_name>verify_email_at_signup</var_name>
			<phrase_var_name>setting_verify_email_at_signup</phrase_var_name>
			<ordering>1</ordering>
			<version_id>2.0.0beta5</version_id>
			<value>0</value>
		</setting>
		<setting>
			<group>registration</group>
			<module_id>user</module_id>
			<is_hidden>0</is_hidden>
			<type>integer</type>
			<var_name>on_signup_new_friend</var_name>
			<phrase_var_name>setting_on_signup_new_friend</phrase_var_name>
			<ordering>1</ordering>
			<version_id>2.0.0rc4</version_id>
			<value>0</value>
		</setting>
	</phpfox_update_settings>
	<phrases>
		<phrase>
			<module_id>user</module_id>
			<version_id>2.0.0rc11</version_id>
			<var_name>enable_dst_daylight_savings_time</var_name>
			<added>1259938658</added>
			<value>Enable DST (Daylight Savings Time)</value>
		</phrase>
		<phrase>
			<module_id>user</module_id>
			<version_id>2.0.0rc11</version_id>
			<var_name>setting_date_of_birth_start</var_name>
			<added>1259943344</added>
			<value><![CDATA[<title>Date of Birth (Start)</title><info>Date of Birth (Start)</info>]]></value>
		</phrase>
		<phrase>
			<module_id>user</module_id>
			<version_id>2.0.0rc11</version_id>
			<var_name>setting_date_of_birth_end</var_name>
			<added>1259943587</added>
			<value><![CDATA[<title>Date of Birth (End)</title><info>Date of Birth (End)</info>]]></value>
		</phrase>
		<phrase>
			<module_id>user</module_id>
			<version_id>2.0.0rc11</version_id>
			<var_name>your_user_name</var_name>
			<added>1259960987</added>
			<value>your-user-name</value>
		</phrase>
		<phrase>
			<module_id>user</module_id>
			<version_id>2.0.0rc11</version_id>
			<var_name>forum_signature</var_name>
			<added>1260027584</added>
			<value>Forum Signature</value>
		</phrase>
		<phrase>
			<module_id>user</module_id>
			<version_id>2.0.0rc11</version_id>
			<var_name>space_total_out_of_unlimited</var_name>
			<added>1260197948</added>
			<value><![CDATA[{space_total} out of "unlimited"]]></value>
		</phrase>
		<phrase>
			<module_id>user</module_id>
			<version_id>2.0.0rc11</version_id>
			<var_name>space_total_out_of_total</var_name>
			<added>1260198034</added>
			<value>{space_total} out of {total} Mb</value>
		</phrase>
		<phrase>
			<module_id>user</module_id>
			<version_id>2.0.0rc11</version_id>
			<var_name>sorry_no_members_found</var_name>
			<added>1260198777</added>
			<value>Sorry, no members found.</value>
		</phrase>
		<phrase>
			<module_id>user</module_id>
			<version_id>2.0.0rc11</version_id>
			<var_name>user_setting_can_search_user_gender</var_name>
			<added>1260199675</added>
			<value>Can search a users gender using the browse filter?</value>
		</phrase>
		<phrase>
			<module_id>user</module_id>
			<version_id>2.0.0rc11</version_id>
			<var_name>user_setting_can_search_user_age</var_name>
			<added>1260199727</added>
			<value>Can search for users based on their age using the browse filter?</value>
		</phrase>
		<phrase>
			<module_id>user</module_id>
			<version_id>2.0.0rc11</version_id>
			<var_name>register_for_an_account</var_name>
			<added>1260232205</added>
			<value>Register for An Account</value>
		</phrase>
		<phrase>
			<module_id>user</module_id>
			<version_id>2.0.0rc11</version_id>
			<var_name>user_setting_can_browse_users_in_public</var_name>
			<added>1260276391</added>
			<value>Can browse users using the public browse section?</value>
		</phrase>
		<phrase>
			<module_id>user</module_id>
			<version_id>2.0.0rc11</version_id>
			<var_name>title_featured_members</var_name>
			<added>1260209155</added>
			<value>Featured Members</value>
		</phrase>
		<phrase>
			<module_id>user</module_id>
			<version_id>2.0.0rc11</version_id>
			<var_name>title_who_s_online</var_name>
			<added>1260209680</added>
			<value><![CDATA[Who's Online]]></value>
		</phrase>
	</phrases>
	<phpfox_update_phrases>
		<phrase>
			<module_id>user</module_id>
			<version_id>2.0.0rc4</version_id>
			<var_name>click_here_to_learn_more_about_our_membership_upgrades</var_name>
			<added>1255350114</added>
			<value>Click here to learn more about our memberships.</value>
		</phrase>
	</phpfox_update_phrases>
	<user_group_settings>
		<setting>
			<is_admin_setting>0</is_admin_setting>
			<module_id>user</module_id>
			<type>boolean</type>
			<admin>1</admin>
			<user>1</user>
			<guest>1</guest>
			<staff>1</staff>
			<module>user</module>
			<ordering>0</ordering>
			<value>can_search_user_gender</value>
		</setting>
		<setting>
			<is_admin_setting>0</is_admin_setting>
			<module_id>user</module_id>
			<type>boolean</type>
			<admin>1</admin>
			<user>1</user>
			<guest>1</guest>
			<staff>1</staff>
			<module>user</module>
			<ordering>0</ordering>
			<value>can_search_user_age</value>
		</setting>
		<setting>
			<is_admin_setting>0</is_admin_setting>
			<module_id>user</module_id>
			<type>boolean</type>
			<admin>1</admin>
			<user>1</user>
			<guest>1</guest>
			<staff>1</staff>
			<module>user</module>
			<ordering>0</ordering>
			<value>can_browse_users_in_public</value>
		</setting>
	</user_group_settings>
	<hooks>
		<hook>
			<module_id>user</module_id>
			<hook_type>component</hook_type>
			<module>user</module>
			<call_name>user.block_login-block_process__start</call_name>
			<added>1260366442</added>
			<version_id>2.0.0rc11</version_id>
			<value />
		</hook>
		<hook>
			<module_id>user</module_id>
			<hook_type>component</hook_type>
			<module>user</module>
			<call_name>user.block_login-block_process__end</call_name>
			<added>1260366442</added>
			<version_id>2.0.0rc11</version_id>
			<value />
		</hook>
	</hooks>
	<phpfox_update_custom_group>
		<group>
			<module_id>user</module_id>
			<type_id>user_profile</type_id>
			<phrase_var_name>user.custom_group_about_me</phrase_var_name>
			<is_active>1</is_active>
			<ordering>1</ordering>
			<value />
		</group>
		<group>
			<module_id>user</module_id>
			<type_id>user_profile</type_id>
			<phrase_var_name>user.custom_group_interests</phrase_var_name>
			<is_active>1</is_active>
			<ordering>2</ordering>
			<value />
		</group>
	</phpfox_update_custom_group>
	<phpfox_update_custom_field>
		<field>
			<group_name>user.custom_group_about_me</group_name>
			<field_name>about_me</field_name>
			<module_id>user</module_id>
			<type_id>user_main</type_id>
			<phrase_var_name>user.custom_about_me</phrase_var_name>
			<type_name>MEDIUMTEXT</type_name>
			<var_type>textarea</var_type>
			<is_active>1</is_active>
			<is_required>0</is_required>
			<ordering>1</ordering>
			<value />
		</field>
		<field>
			<group_name>user.custom_group_about_me</group_name>
			<field_name>who_i_d_like_to_meet</field_name>
			<module_id>user</module_id>
			<type_id>user_main</type_id>
			<phrase_var_name>user.custom_who_i_d_like_to_meet</phrase_var_name>
			<type_name>MEDIUMTEXT</type_name>
			<var_type>textarea</var_type>
			<is_active>1</is_active>
			<is_required>0</is_required>
			<ordering>2</ordering>
			<value />
		</field>
		<field>
			<group_name>user.custom_group_interests</group_name>
			<field_name>interests</field_name>
			<module_id>user</module_id>
			<type_id>profile_panel</type_id>
			<phrase_var_name>user.custom_interests</phrase_var_name>
			<type_name>MEDIUMTEXT</type_name>
			<var_type>textarea</var_type>
			<is_active>1</is_active>
			<is_required>0</is_required>
			<ordering>4</ordering>
			<value />
		</field>
		<field>
			<group_name>user.custom_group_interests</group_name>
			<field_name>music</field_name>
			<module_id>user</module_id>
			<type_id>profile_panel</type_id>
			<phrase_var_name>user.custom_music</phrase_var_name>
			<type_name>MEDIUMTEXT</type_name>
			<var_type>textarea</var_type>
			<is_active>1</is_active>
			<is_required>0</is_required>
			<ordering>5</ordering>
			<value />
		</field>
		<field>
			<group_name>user.custom_group_interests</group_name>
			<field_name>movies</field_name>
			<module_id>user</module_id>
			<type_id>profile_panel</type_id>
			<phrase_var_name>user.custom_movies</phrase_var_name>
			<type_name>MEDIUMTEXT</type_name>
			<var_type>textarea</var_type>
			<is_active>1</is_active>
			<is_required>0</is_required>
			<ordering>3</ordering>
			<value />
		</field>
		<field>
			<group_name>user.custom_group_details</group_name>
			<field_name>smoker</field_name>
			<module_id>user</module_id>
			<type_id>user_panel</type_id>
			<phrase_var_name>user.custom_smoker</phrase_var_name>
			<type_name>VARCHAR(150)</type_name>
			<var_type>select</var_type>
			<is_active>1</is_active>
			<is_required>0</is_required>
			<ordering>6</ordering>
			<value><![CDATA[a:3:{i:0;a:1:{s:15:"phrase_var_name";s:24:"user.cf_option_sometimes";}i:1;a:1:{s:15:"phrase_var_name";s:17:"user.cf_option_no";}i:2;a:1:{s:15:"phrase_var_name";s:18:"user.cf_option_yes";}}]]></value>
		</field>
		<field>
			<group_name>user.custom_group_details</group_name>
			<field_name>drinker</field_name>
			<module_id>user</module_id>
			<type_id>user_panel</type_id>
			<phrase_var_name>user.custom_drinker</phrase_var_name>
			<type_name>VARCHAR(150)</type_name>
			<var_type>select</var_type>
			<is_active>1</is_active>
			<is_required>0</is_required>
			<ordering>7</ordering>
			<value><![CDATA[a:3:{i:0;a:1:{s:15:"phrase_var_name";s:18:"user.cf_option_yes";}i:1;a:1:{s:15:"phrase_var_name";s:17:"user.cf_option_no";}i:2;a:1:{s:15:"phrase_var_name";s:24:"user.cf_option_sometimes";}}]]></value>
		</field>
	</phpfox_update_custom_field>
	<sql><![CDATA[a:2:{s:9:"ADD_FIELD";a:1:{s:11:"phpfox_user";a:1:{s:9:"dst_check";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}}s:11:"ALTER_FIELD";a:1:{s:17:"phpfox_user_field";a:1:{s:9:"signature";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}}}]]></sql>
	<update_templates>
		<file type="controller">profile.html.php</file>
		<file type="controller">remove.html.php</file>
		<file type="controller">register.html.php</file>
		<file type="controller">setting.html.php</file>
		<file type="controller">browse.html.php</file>
		<file type="block">login-block.html.php</file>
		<file type="block">login-ajax.html.php</file>
		<file type="block">filter.html.php</file>
		<file type="block">setting.html.php</file>
		<file type="block">browse.html.php</file>
	</update_templates>
</upgrade>