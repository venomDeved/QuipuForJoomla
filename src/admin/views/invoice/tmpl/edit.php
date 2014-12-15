<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
?>
<ul class="nav nav-tabs">
    <li class="active"><a href="#tab-info" data-toggle="tab"><?php echo JText::_('COM_QUIPU_ORDER_DATA'); ?></a></li>
    <li><a href="#tab-details" data-toggle="tab"><?php echo JText::_('COM_QUIPU_INVOICE_DETAILS'); ?></a></li>
</ul>
<div class="tab-content">
    <div id="tab-info" class="tab-pane active">
        <form class="item form-validate form-horizontal" action="<?php echo JRoute::_('index.php?option=com_quipu&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm">
            <div class="span4">
                <fieldset class="adminform">
                    <legend><?php echo JText::_('COM_QUIPU_ORDER_DATA'); ?></legend>
                    <?php foreach ($this->form->getFieldset('general') as $field): ?>
                        <div class="control-group">
                            <div class="control-label"><?php echo  $field->label; ?></div>
                            <div class="controls"><?php echo  $field->input; ?></div>
                        </div>
                    <?php endforeach; ?>
                </fieldset>
            </div>
            <div class="span2">
                <fieldset class="adminform">
                    <legend><?php echo JText::_('COM_QUIPU_INVOICE_ACTIONS'); ?></legend>
                    <?php foreach ($this->form->getFieldset('actions') as $field): ?>
                        <div class="control-group">
                            <?php echo  $field->input; ?>
                        </div>
                    <?php endforeach; ?>
                </fieldset>
            </div>
            <div class="span4">
                <fieldset class="adminform">    
                    <legend><?php echo JText::_('COM_QUIPU_MEMO'); ?></legend>
                    <?php foreach ($this->form->getFieldset('memo') as $field): ?>
                        <div class="control-group">
                            <?php echo  $field->input; ?>
                        </div>
                    <?php endforeach; ?>
                </fieldset>        
            </div>
            <input type="hidden" name="task" value="invoice.save" />
            <?php echo JHtml::_('form.token'); ?>

        </form>            
    </div>
    <div id="tab-details" class="tab-pane">
        <div class="span10">
            <form class="item" action="" method="post" name="detailsForm" id="invoice-form-details">        
            <fieldset class="adminform">
                <legend><?php echo JText::_('COM_QUIPU_DETAILS'); ?></legend>
                <?php foreach ($this->form->getFieldset('detail') as $field): ?>
                    <div class="control-group">
                        <div class="control-label"><?php echo  $field->label; ?></div>
                        <div class="controls"><?php echo  $field->input; ?></div>
                    </div>
                <?php endforeach; ?>
            </fieldset> 
            </form>
        </div>

    </div>

    <?php echo  $this->loadTemplate('newrow'); ?>
</div>