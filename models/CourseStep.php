<?php

namespace app\models;


/**
 * This is the model class for table "course_step".
 *
 * @property int $id_step
 * @property int $id_course
 * @property string $name
 * @property int $sort
 * @property string $description
 * @property string $video
 *
 * @property Course $course
 * @property CourseStepSub[] $courseStepSubs
 */
class CourseStep extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'course_step';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_course', ], 'required'],
            [['id_course', 'sort'], 'integer'],
            [['name', 'video'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 1000],
            [['id_course'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['id_course' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_step' => 'Id Step',
            'id_course' => 'Id Course',
            'name' => 'Name',
            'sort' => 'Sort',
            'description' => 'Description',
            'video' => 'Video',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourse()
    {
        return $this->hasOne(Course::class, ['id' => 'id_course']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseStepSubs()
    {
        return $this->hasMany(CourseStepSub::class, ['id_step' => 'id_step']);
    }
}
