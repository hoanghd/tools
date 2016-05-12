<li><a href="{url link='pages.add' id=$aPage.page_id}">{phrase var='pages.manage'}</a></li>
{if Phpfox::getUserParam('pages.can_moderate_pages') || $aPage.user_id == Phpfox::getUserId()}
<li class="item_delete"><a href="{url link='pages' delete=$aPage.page_id}" onclick="return confirm('{phrase var='pages.are_you_sure'}');" class="no_ajax_link">{phrase var='pages.delete'}</a></li>
{/if}