<?php
/**
    Given:  pagetitle,
            actions,
            list :  (id,
                    comment_id,
                    user_name,
                    status,
                    creation_timestamp)
*/
?>

<?php include("$TEMPLATEROOT/template_begin.inc"); ?>
<?php include("$TEMPLATEROOT/template_notices.inc"); ?>

        <table>
            <tr>
                <th>id<hr/></th>
                <th>resource_id<hr/></th>
                <th>resource_subject<hr/></th>
                <th>user_name<hr/></th>
                <th>status<hr/></th>
                <th>creation_timestamp<hr/></th>
            </tr>
<?php   foreach($args['list'] as $elem) {   ?>
            <tr>
                <td><a href="<?php echo $args['actions']['show']->getLink(array('id'=>$elem['id'])); ?>">
                        <?php echo $elem['id']; ?>
                    </a>
                </td>
                <td><?php echo $elem['resource_id'].' ('.$elem['type'].')'; ?></td>
                <td><?php echo limit($elem['resource_subject'], '', 40); ?></td>
                <td><?php echo $elem['user_name']; ?></td>
                <td><?php echo $elem['status'] == 0 ? 'open' : 'closed'; ?></td>
                <td><?php echo $elem['creation_timestamp']; ?></td>
            </tr>
<?php   }   ?>
		</table>

<?php include("$TEMPLATEROOT/template_end.inc"); ?>
