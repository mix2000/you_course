<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\CourseStep;
use app\models\CourseStepSub;
use app\supportClass\Parser;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Query;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DownloadController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @return int Exit code
     */
    public function actionIndex()
    {
        $domain = 'https://yougifted.ru';
        $query = new Query();
        $result = $query->select(['id_course', 'id_yougifted'])->from('course_parser')->all();

        foreach ($result as $link) { // перебираем курсы
            $linkParser = $domain . '/personal/training?pack=' . $link['id_yougifted'];
            // скачиваем и собраем оттуда тренировки, которые есть.
            $path = $this->parsePage($linkParser);
            $this->getTrainingCourse($path,$link['id_course']);

        }
        return ExitCode::OK;
    }




    public function getTrainingCourse($path = '',$idCourse)
    {
        $filePath = ($path === '')?'temp/personal_training.html':$path;
        \phpQuery::newDocumentFileHTML($filePath);
        $trainingList = pq('.training-frame-list a');
        $step = 1;
        foreach ($trainingList as $list) {
            // вытаскиваем все тренироки
            echo $list->getAttribute('href') . " - {$idCourse}\n\r ";
            $result = $this->actionGetStep($list->getAttribute('href'), $step,$idCourse);
            if ($result === false) {
                break;
            }
            $step++;
        }
    }

    /**
     * @param string $page
     * @param $sortStep
     * @param $idCourse
     * @return bool
     */
    public function actionGetStep($page = '/personal/training?id=730&pack=34', $sortStep, $idCourse)
    {
        $domain = 'https://yougifted.ru';
        $page = $domain . $page . '&anyway=true';
        $path = $this->parsePage($page);
        if ($path === '') {
            return false;
        }
        $courseStep = new CourseStep();
        \phpQuery::newDocumentFileHTML($path);
        $titleElement = pq('.name-traning')->eq(0);
        $courseStep->sort = $sortStep;
        $courseStep->name = $titleElement->text(); // получили название
        $courseStep->id_course = $idCourse;
        $courseStep->description = pq('.description-traning')->eq(0)->html();
        $iframe = pq('#video-frame');
        $linkYoutube = $iframe->attr('src'); // получаем линки
        $keyYoutube = $this->getYouTubeLink($linkYoutube); // извлекаем ключ
        $courseStep->video = $keyYoutube;
        $courseStep->save();

        $steps = pq('#steps > div');

        $i = 1;
        if ($steps->length > 0) {
            foreach ($steps as $step) {
                $this->addSubStep($step,$courseStep->id_step,$i);
                $i++;
                $step->getAttribute('class');
            }
        }
        return true;
    }

    /**
     * @param $step
     * @param $idCourseStep integer
     * @param $sort integer
     */
    protected function addSubStep($step, $idCourseStep, $sort)
    {
        $keyYoutube = '';
        $text = '';
        $classStep = $step->getAttribute('class');
        $stepSub = pq($step);
        if (stripos($classStep, 'training-steps-header') !== false) {
            // просто
            $text = trim($stepSub->find('.training-steps-description')->text());
        }
        if (stripos($classStep, 'training-steps-row') !== false) {
            $tagA = $stepSub->find('a')->eq(0);
            if ($tagA->length) {
                $data = $tagA->attr('onclick');
                $keyYoutube = $this->getyoutibeLinkVersion2($data);
            }
            $text = trim($stepSub->find('.training-steps-description')->eq(0)->text());
        }
        $courseStepSub = new CourseStepSub();
        $courseStepSub->id_step = $idCourseStep;
        $courseStepSub->sort = $sort;
        $courseStepSub->name = $text;
        $courseStepSub->video_key = $keyYoutube;
        $courseStepSub->save();
    }

    protected function parsePage($url,$sleep = 1)
    {
        //
        $parser = new Parser();
        $parser
            ->set(CURLOPT_COOKIEJAR, 'temp/cookie-jar.txt')
            ->set(CURLOPT_COOKIEFILE, 'temp/cookie-file.txt')
            ->set(CURLOPT_ENCODING, 'gzip')
            ->set(CURLOPT_HTTPHEADER,
                \Yii::$app->params['cookie']
            );
        $data = $parser->exec($url);
        if($sleep>0){
            sleep(1);
        }
        if ($data === '') {
            return '';
        }
        $path = 'temp/' . uniqid('prefix', true) . '.html';
        file_put_contents($path, $data);
        return $path;
    }

    protected function getYouTubeLink($text)
    {
        $text = preg_replace('~(?#!js YouTubeId Rev:20160125_1800)
        # Match non-linked youtube URL in the wild. (Rev:20130823)
        https?://          # Required scheme. Either http or https.
        (?:[0-9A-Z-]+\.)?  # Optional subdomain.
        (?:                # Group host alternatives.
          youtu\.be/       # Either youtu.be,
        | youtube          # or youtube.com or
          (?:-nocookie)?   # youtube-nocookie.com
          \.com            # followed by
          \S*?             # Allow anything up to VIDEO_ID,
          [^\w\s-]         # but char before ID is non-ID char.
        )                  # End host alternatives.
        ([\w-]{11})        # $1: VIDEO_ID is exactly 11 chars.
        (?=[^\w-]|$)       # Assert next char is non-ID or EOS.
        (?!                # Assert URL is not pre-linked.
          [?=&+%\w.-]*     # Allow URL (query) remainder.
          (?:              # Group pre-linked alternatives.
            [\'"][^<>]*>   # Either inside a start tag,
          | </a>           # or inside <a> element text contents.
          )                # End recognized pre-linked alts.
        )                  # End negative lookahead assertion.
        [?=&+%\w.-]*       # Consume any URL (query) remainder.
        ~ix', '$1',
            $text);
        return $text;
    }

    protected function getIdTraining($url)
    {
        $parts = parse_url($url);
        parse_str($parts['id'], $query);
        return $query['id'];
    }

    protected function getyoutibeLinkVersion2($text)
    {
        return preg_replace('/changeVideo\\(\'(.*?)\'\\)/sui', '$1', $text, -1, $count);
    }
}
