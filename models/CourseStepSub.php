<?php

namespace app\models;

/**
 * This is the model class for table "course_step_sub".
 *
 * @property int $id_step_sub
 * @property int $id_step
 * @property int $sort
 * @property string $name
 * @property string $description
 * @property string $video_key
 * @property string $photo
 *
 * @property CourseStep $step
 */
class CourseStepSub extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'course_step_sub';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_step', 'sort', 'name', 'description', 'video_key', 'photo'], 'required'],
            [['id_step', 'sort'], 'integer'],
            [['name', 'video_key', 'photo'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 1000],
            [['id_step'], 'exist', 'skipOnError' => true, 'targetClass' => CourseStep::class, 'targetAttribute' => ['id_step' => 'id_step']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_step_sub' => 'Id Step Sub',
            'id_step' => 'Id Step',
            'sort' => 'Sort',
            'name' => 'Name',
            'description' => 'Description',
            'video_key' => 'Video Key',
            'photo' => 'Photo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStep()
    {
        return $this->hasOne(CourseStep::class, ['id_step' => 'id_step']);
    }
}
