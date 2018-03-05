<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Monitor */
/* @var $form yii\widgets\ActiveForm */
/* @var attributes array */
?>

<div class="monitor-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => 'MonitorForm']]); ?>
    <div class="row attibutes">
        <?php foreach ($attributes as $key => $value): ?>
            <div class="col-sm-3">
                <div class="form-group">
                    <?= "<label for='monitor-form-$key'>$key</label><br>" ?>
                    <?= "<input id='monitor-form-$key' type='text' name='MonitorForm[$key]' value='$value'>" ?>
                </div>
            </div>

        <?php endforeach; ?>
    </div>



    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <a id="AddAttributeToForm" class="btn btn-primary" onclick="$('#NewAttributeCreator').show()">Add attribute</a>
    </div>

    <?php ActiveForm::end(); ?>
    <br>
    <br>
    <div id="NewAttributeCreator" style="display: none;">
        <label for="NewAttributeName">New attribute name</label><br>
        <input id="NewAttributeName" type="text"><br><br>
        <a id="AddAttributeToForm" class="btn btn-success" onclick="addAttributeToForm()">Add to form</a>
    </div>

</div>


<script>
    function addAttributeToForm() {
        var form = $('.attibutes');
        var newAttributeName = $('#NewAttributeName').val();
        if(newAttributeName) {
            form.append('<div class="col-sm-3"><div class="form-group">' +
                '<label for="monitor-form-'+newAttributeName+'">'+newAttributeName+'</label><br>' +
                '<input id="monitor-form-'+newAttributeName+'" name="MonitorForm['+newAttributeName+']" type="text">' +
                '</div></div>');
            $('#NewAttributeName').val('');
        }

    }
</script>
