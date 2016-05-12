<module>
	<data>
		<module_id>janrain</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_janrain</phrase_var_name>
		<writable />
	</data>
	<settings>
		<setting group="" module_id="janrain" is_hidden="0" type="boolean" var_name="enable_janrain_login" phrase_var_name="setting_enable_janrain_login" ordering="1" version_id="3.0.0beta1">0</setting>
		<setting group="" module_id="janrain" is_hidden="0" type="string" var_name="janrain_api_key" phrase_var_name="setting_janrain_api_key" ordering="1" version_id="3.0.0beta1" />
		<setting group="" module_id="janrain" is_hidden="0" type="string" var_name="janrain_application_domain" phrase_var_name="setting_janrain_application_domain" ordering="1" version_id="3.0.0beta1" />
	</settings>
	<phrases>
		<phrase module_id="janrain" version_id="3.0.0beta1" var_name="module_janrain" added="1306309103">Janrain</phrase>
		<phrase module_id="janrain" version_id="3.0.0beta1" var_name="setting_enable_janrain_login" added="1306312125"><![CDATA[<title>Enable Janrain Integration</title><info>Set this to "True" to enable this integration.</info>]]></phrase>
		<phrase module_id="janrain" version_id="3.0.0beta1" var_name="setting_janrain_api_key" added="1306312237"><![CDATA[<title>Janrain API Key (Secret)</title><info>Enter your Jainrain API Key (Secret) here.</info>]]></phrase>
		<phrase module_id="janrain" version_id="3.0.0beta1" var_name="setting_janrain_application_domain" added="1306314080"><![CDATA[<title>Application Domain</title><info>Enter your "Application Domain" here.</info>]]></phrase>
	</phrases>
	<tables><![CDATA[a:1:{s:14:"phpfox_janrain";a:2:{s:7:"COLUMNS";a:3:{s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"identifier";a:4:{i:0;s:7:"CHAR:32";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:1:{s:7:"user_id";a:2:{i:0;s:6:"UNIQUE";i:1;s:7:"user_id";}}}}]]></tables>
</module>