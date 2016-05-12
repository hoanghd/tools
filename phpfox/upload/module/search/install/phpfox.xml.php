<module>
	<data>
		<module_id>search</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_search</phrase_var_name>
		<writable />
	</data>
	<hooks>
		<hook module_id="search" hook_type="controller" module="search" call_name="search.component_controller_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="search" hook_type="service" module="search" call_name="search.service_search__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="search" hook_type="service" module="search" call_name="search.service_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="search" hook_type="service" module="search" call_name="search.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="search" hook_type="controller" module="search" call_name="search.component_controller_tag_clean" added="1259160644" version_id="2.0.0rc9" />
	</hooks>
	<phrases>
		<phrase module_id="search" version_id="2.0.0alpha1" var_name="module_search" added="1232969305">Search</phrase>
		<phrase module_id="search" version_id="2.0.0rc8" var_name="provide_a_search_query" added="1258738780">Provide a search query.</phrase>
		<phrase module_id="search" version_id="2.0.0rc8" var_name="tags" added="1258740328">Tags</phrase>
		<phrase module_id="search" version_id="2.0.0rc8" var_name="results" added="1259089709">Results</phrase>
		<phrase module_id="search" version_id="2.0.0rc8" var_name="search" added="1259089719">Search</phrase>
		<phrase module_id="search" version_id="2.0.0rc8" var_name="results_for" added="1259089736">Results for</phrase>
		<phrase module_id="search" version_id="2.0.7" var_name="user_setting_can_use_global_search" added="1288616524">Can use the global search tool?</phrase>
	</phrases>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="search" type="boolean" admin="1" user="1" guest="1" staff="1" module="search" ordering="0">can_use_global_search</setting>
	</user_group_settings>
</module>