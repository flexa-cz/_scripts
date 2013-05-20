<?php
// nezbytnosti
require_once('../core/include.php');
debuger::set_enable_report(true);
$core=new Core;
$core->site->setTitle('export sites_parts from propeople cms backup');
$core->site->addHeader('<style type="text/css">'.debuger::get_css().'</style>');

$core->db->setMysqlDatabase('propeople_cms_backup')->connect();


$core->db->query("set @domain = 'spektrumzdravi.cz'");
$core->db->query("set @root_relation_id = (select id_relations from settings where `name`='alias' and `value`=@domain AND `attribut`='PREFERED')");
$core->db->query("set @root_lft = (select lft from relations where id=@root_relation_id)");
$core->db->query("set @root_rgt = (select rgt from relations where id=@root_relation_id)");



echo $core->site;
//select @root_relation_id,@root_lft,@root_rgt;

$query="
	select sp.* from sites_parts as sp
	left join sites_parts_2 as sp2 on sp2.id=sp.id
	left join relations as r on r.id_sites = sp.id_sites
	where
		sp.part='text' and
		sp.content!='' and
		sp2.content='' and
		r.lft>@root_lft and
		r.rgt<@root_rgt";

$count=0;
foreach($core->db->query($query)->getRows() AS $row){
	$count++;
	$query="UPDATE sites_parts
SET `content`=\"".mysql_real_escape_string($row['content'])."\"
WHERE `id`=".$row['id']." AND `content`='';";
	echo '<pre>'.htmlspecialchars($query).'</pre>';
}
echo '# celkem polozek: '.$count;