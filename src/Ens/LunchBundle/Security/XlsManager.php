<?php
/**
 * Created by PhpStorm.
 * User: d.baryshev
 * Date: 03.06.2015
 * Time: 16:46
 */

namespace Ens\LunchBundle\Security;

use Ens\LunchBundle\Entity\Lunch;
use PHPExcel_IOFactory;
use Symfony\Bridge\Doctrine\ManagerRegistry;


class XlsManager {

    private $dateperiod;
    protected $em;

    function __construct($em)
    {
        $this->dateperiod = date("d.m.Y", strtotime("last Monday")).'-'.date("d.m.Y", strtotime("Sunday"));
        $this->em = $em;
    }

    /**
     * @return mixed
     * @throws \PHPExcel_Reader_Exception
     */
    public function parseXlsFile()
    {
        $inputFileName = __DIR__.'/../../../../web/uploads/documents/'.$this->dateperiod.'_menu.xlsx';

//  Read your Excel workbook
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName, PATHINFO_BASENAME).'": '.$e->getMessage());
        }

//  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $arrayLabel = array('B', 'D', 'F', 'H', 'J');

        /** @var string[] $categories */
        $categories = [
            'Salad',
            'Main_Course',
            'Soup',
            'Dessert',
        ];

        /** @var string[] $days */
        $days = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
        ];

        /** @var integer $num_category */
        $num_category = 0;

        /** @var integer $num */
        $num = 0;

        //The last lunches are not active

        $lunches = $this->em->getRepository('EnsLunchBundle:Lunch')->getActiveLunches();

        foreach ($lunches as $item) {
            $item->setActive(0);
            $this->em->persist($item);
        }

//  Loop through each row of the worksheet in turn
        for ($row = 2; $row <= $highestRow; $row++) {
            $num++;
            if ($sheet->getCell('B'.$row)->getValue() != '') {
                for ($column = 0; $column < count($arrayLabel); $column++) {
                    $description = $sheet->getCell($arrayLabel[$column].$row)->getValue();
                    $lunch = new Lunch();
// display each cell value
                    $lunch->setCategories($categories[$num_category]);
                    $lunch->setDay($days[$column]);
                    $lunch->setCount($num);
                    $lunch->setActive(1);
                    $lunch->setDescription($description);
                    $this->em->persist($lunch);
                }
            } else {
                $num_category++;
                $num = 0;
            }
            if ($num_category == 4) {
                break;
            }
        }
        $this->em->flush();
    }

    /**
     * @return mixed
     * @throws \PHPExcel_Reader_Exception
     */
    public function writeOrderXlsFile($names, $userChoices, $floor)
    {
        $inputFileName = __DIR__.'/../../../../web/uploads/orders/form_order.xlsx';

//  Read your Excel workbook
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName, PATHINFO_BASENAME).'": '.$e->getMessage());
        }

//  Get worksheet dimensions
        $sheet = $objPHPExcel->setActiveSheetIndex(0);
        $firstRow = 2;
        $countOfDishes = 4;
        $highestRow = $firstRow + $countOfDishes * count($names) - 1;
        $arrayLabel = array('B', 'C', 'D', 'E', 'F', 'G');

        /** @var string[] $categoriesRus */
        $categoriesRus = [
            'Салат',
            'Горячее',
            'Суп',
            'Дессерт',
        ];

        /** @var string[] $categoriesRus */
        $daysRus = [
            'Пн.',
            'Вт.',
            'Ср.',
            'Чт.',
            'Пт.',
        ];

// Change the file
        $numName = 0;
        $numCategory = 0;
        $numChoice = 0;
        $numLabel = 1;

        //insert name
        foreach ($names as $name) {
            $sheet->setCellValue('A'.($firstRow + $numName * $countOfDishes), 'ФИО');
            $partsOfName = explode(" ", $name);
            $sheet->setCellValue('A'.($firstRow + $numName * $countOfDishes + 1), $partsOfName[0]);
            $sheet->setCellValue('A'.($firstRow + $numName * $countOfDishes + 2), $partsOfName[1]);
            $sheet->setCellValue('A'.($firstRow + $numName * $countOfDishes + 3), $partsOfName[2]);
            $numName++;
        }

        //insert days of week
        foreach ($daysRus as $day) {
            $sheet->setCellValue($arrayLabel[$numLabel].'1', $day);
            $numLabel++;
        }

        //insert user choices and categories of dishes
        for ($row = $firstRow; $row <= $highestRow; $row++) {
            foreach ($arrayLabel as $column) {
                if ($column == 'B') {
                    $sheet->setCellValue($column.$row, $categoriesRus[$numCategory]);
                    $numCategory++;
                    if ($numCategory == $countOfDishes) {
                        $numCategory = 0;
                    }
                } else {
                    $sheet->setCellValue($column.$row, $userChoices[$numChoice]);
                    $numChoice++;
                }
            }
        }

// Write the file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $inputFileType);
        $objWriter->save(__DIR__.'/../../../../web/uploads/orders/'.$this->dateperiod.'_'.$floor.'_order.xlsx');
    }

    public function writeMenuXlsFile($floor)
    {
        $inputFileName = __DIR__.'/../../../../web/uploads/documents/'.$this->dateperiod.'_menu.xlsx';

//  Read your Excel workbook
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName, PATHINFO_BASENAME).'": '.$e->getMessage());
        }

//  Get worksheet dimensions
        $firstRow = 2;
        $sheet = $objPHPExcel->setActiveSheetIndex(0);
        $highestRow = $sheet->getHighestRow();
        $arrayLabel = array('C', 'E', 'G', 'I', 'K');

        //get the number of servings of each dish
        $numServings = [];
        $lunches = $this->em->getRepository('EnsLunchBundle:Lunch')->getActiveLunches();
        foreach ($lunches as $lunch) {
            $countOneLunch = count(
                $this->em->getRepository(
                    'EnsLunchBundle:Jointable'
                )->getActiveJoinsByOneLunchAndFloor(
                    $lunch,
                    $floor
                )
            );
            array_push($numServings, $countOneLunch);
        }

// Change the file
        $numLunch = 0;

        //insert user choices and categories of dishes
        for ($row = $firstRow; $row <= $highestRow; $row++) {
            foreach ($arrayLabel as $column) {
                if ($sheet->getCell('A'.$row)->getValue() != '') {
                    $sheet->setCellValue($column.$row, $numServings[$numLunch]);
                    $numLunch++;
                }
            }
        }

// Write the file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $inputFileType);
        $objWriter->save(__DIR__.'/../../../../web/uploads/orders/'.$this->dateperiod.'_'.$floor.'_menu.xlsx');
    }

}