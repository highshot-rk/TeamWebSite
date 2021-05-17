<?php if ( $permit != true ) return; ?>



<div class="dpr-switch-tab-wrap"><!-- deeper switch-tab wrapper /start -->

	<ul class="dpr-switch-tab">
		<li class="dpr-switch-tab-newest"><a href="#" class="dpr-switch-tab-newest-a dpr-tooltip <?php echo ( isset( $default ) &&  $default == 'newest' ) ? 'dpr-active-tab' : ''; ?>" data-wntooltip="<?php echo $i18n['sort'] . $i18n['newest'] . $i18n['item']; ?>"><?php echo $i18n['newest']; ?></a></li>
		<li class="dpr-switch-tab-oldest"><a href="#" class="dpr-switch-tab-oldest-a dpr-tooltip <?php echo ( isset( $default ) && $default == 'oldest' ) ? 'dpr-active-tab' : ''; ?>" data-wntooltip="<?php echo $i18n['sort'] . $i18n['oldest'] . $i18n['item']; ?>"><?php echo $i18n['oldest']; ?></a></li>
		<li class="dpr-switch-tab-popular"><a href="#" class="dpr-switch-tab-popular-a dpr-tooltip <?php echo ( isset( $default ) &&  $default == 'popular' ) ? 'dpr-active-tab' : ''; ?>" data-wntooltip="<?php echo $i18n['sort'] . $i18n['popular'] . $i18n['item']; ?>"><?php echo $i18n['popular']; ?></a></li>
		<li class="dpr-switch-tab-trending"><a href="#" class="dpr-switch-tab-trending-a dpr-tooltip <?php echo ( isset( $default ) && $default == 'trending' ) ? 'dpr-active-tab' : ''; ?>" data-wntooltip="<?php echo $i18n['sort'] . $i18n['trending'] . $i18n['item']; ?>"><?php echo $i18n['trending']; ?></a></li>
	</ul>

	<select class="dpr-switch-dropdown">
	<option class="dpr-switch-dropdown-trending" value=""<?php echo ( isset( $default ) && $default == 'trending' ) ? 'selected="selected"' : ''; ?> ><?php echo $i18n['trending']; ?></option>
		<option class="dpr-switch-dropdown-popular" value="" <?php echo ( isset( $default ) &&  $default == 'popular' ) ? 'selected="selected"' : ''; ?> ><?php echo $i18n['popular']; ?></option>
		<option class="dpr-switch-dropdown-oldest" value="" <?php echo ( isset( $default ) &&  $default == 'oldest' ) ? 'selected="selected"' : ''; ?> ><?php echo $i18n['oldest']; ?></option>
		<option class="dpr-switch-dropdown-newest" value="" <?php echo ( isset( $default ) &&  $default == 'newest' ) ? 'selected="selected"' : ''; ?> ><?php echo $i18n['newest']; ?></option>

	</select>

	<div class="dpr-switch-search-wrap"><input name="search" type="text" placeholder="" class="dpr-discu-search"><i class="sl-magnifier"></i></div>


</div><!-- deeper switch-tab wrapper /end -->
