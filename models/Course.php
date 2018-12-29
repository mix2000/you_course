<?php

namespace app\models;


/**
 * This is the model class for table "course".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $logo
 *
 * @property CourseStep[] $courseSteps
 */
class Course extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'course';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', ], 'required'],
            [['name'], 'string', 'max' => 150],
            [['description'], 'string', 'max' => 1000],
            [['logo'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'logo' => 'Logo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseSteps()
    {
        return $this->hasMany(CourseStep::className(), ['id_course' => 'id']);
    }
}
