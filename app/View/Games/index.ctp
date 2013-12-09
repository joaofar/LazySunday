<div class="games index">
    <table cellpadding="0" cellspacing="0">
    <tr>

        <th><h2><?php echo $this->Paginator->sort('id', 'id');?></h2></th>

        <th><h2>link</h2></th>
        <th><h2><?php echo $this->Paginator->sort('Team.0.score', 'A');?></h2></th>
        <th><h2><?php echo $this->Paginator->sort('Team.1.score', 'B');?></h2></th>
        <th><h2><?php echo $this->Paginator->sort('date', 'data');?></h2></th>
    </tr>
    <?php foreach ($games as $game): ?>
    <tr>
        
        <td><?php echo h($game['Game']['id']); ?>&nbsp;</td>

        <td><?php echo $this->Html->link(__('ver jogo >>>'), array('action' => $game['Game']['stage'], $game['Game']['id'])); ?>&nbsp;</td>
        <td><?php echo h($game['Team'][0]['score']); ?>&nbsp;</td>
        <td><?php echo h($game['Team'][1]['score']); ?>&nbsp;</td>
        <td><?php echo h($this->Time->format('d M, Y', $game['Game']['date'])); ?>&nbsp;</td>

    </tr>
<?php endforeach; ?>
    </table>
    <p>
    <?php
    echo $this->Paginator->counter(array(
    'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
    ));
    ?>  </p>

    <div class="paging">
    <?php
        echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
        echo $this->Paginator->numbers(array('separator' => ''));
        echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
    ?>
    </div>
</div>
