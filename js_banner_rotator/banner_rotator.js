/**
 * vypise object s flash bannerem
 *
 * @param banners dvourozmerne pole ve tvaru arr[0]=('addr'='umisteni flashe', 'link'='cilova adresa')
 * @param width sirka banneru
 * @param height vyska banneru
 */
function banner_rotator(banners,width,height){
	var count = banners.length;
	var i = Math.round(Math.random()*(count-1));
	document.write(get_flash_object(width,height,banners[i]['addr'],banners[i]['link']));
}

/**
* korektni poskladani objeku s flash prvkem
*
* @param width sirka banneru
* @param height vyska banneru
* @param addr kde je banner na disku
* @param link cilova adresa banneru
*/
function get_flash_object(width,height,addr,link){
	var ret='';

	// kvuli ampersandum
	link=escape(link);

	ret+='<!--[if !IE]> -->';
	ret+='<object type="application/x-shockwave-flash" ';
	ret+='data="' + addr + '?clickthru=' + link + '" width="'+ width + '" height="' + height + '">';
	ret+='<!-- <![endif]-->';
	ret+='<!--[if IE]>';
	ret+='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ';
	ret+='codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" ';
	ret+='width="'+ width + '" height="' + height + '">';
	ret+='<param name="movie" value="' + addr + '?clickthru=' + link + '" />';
	ret+='<!-->';
	ret+='<param name="loop" value="true" />';
	ret+='<param name="menu" value="false" />';
	ret+='<p>váš prohlížeč nepodporuje zobrazení flash  prvků na stránce, nebo došlo k chybě při načítání banneru</p>';
	ret+='</object>';
	ret+='<!-- <![endif]-->';

	return ret;
}