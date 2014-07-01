<?php


/**
 * A code sample which converts odd integers to word strings and returns the sum of the common factors of even numbers.
 *
 * The input is assumed to be a random list of integers. For the sum of the common factors of even numbers
 * only positive values are taken into consideration
 */


error_reporting(E_ALL);
function exception_handler($exception) {
  echo get_class($exception). ' : ' . $exception->getMessage(), "\n";
}
set_exception_handler('exception_handler');


//start
$iB = new IntelligentBee(array(-2,6,12, 7, 101, 67));
$iB->processOddNumbers();
$a = $iB->processEvenNumbers();
var_dump($a);
//end


class IntelligentBee{

	const OneHundred = 100;
	const OneThousand = 1000;
	const OneMillion = 1000000;
	const OneBillion = 1000000000;
	protected $minCommonFactors = [];
	protected $oddNumbers = [];
	protected $evenNumbers = [];
	protected $startInt;
	protected $endInt;
	protected static $dictionary = array(
				0=>'zero',
				1=>'one',
				2=>'two',
				3=>'three',
				4=>'four',
				5=>'five',
				6=>'six',
				7=>'seven',
				8=>'eight',
				9=>'nine',
				10=>'ten',
				11=>'eleven',
				12=>'twelve',
				13=>'thirteen',
				14=>'fourteen',
				15=>'fifthteen',
				16=>'sixteen',
				17=>'seventeen',
				18=>'eighteen',
				19=>'nineteen',
				20=>'twenty',
				30=>'thirty',
				40=>'forty',
				50=>'fifty',
				60=>'sixty',
				70=>'seventy',
				80=>'eighty',
				90=>'ninety',
				self::OneHundred=>'hundred',
				self::OneThousand=>'thousand',
				self::OneMillion=>'million',
				self::OneBillion=>'billion'//might get into memory issues
			);

	public function __construct(array $arrayOfIntegers){

		$startInt = min($arrayOfIntegers);
		$endInt = max($arrayOfIntegers);

		if(abs($startInt) > self::OneBillion || abs($startInt) > self::OneBillion){
			throw new OutOfRangeException(sprintf("We can only process numbers between %s and %s! %s provided!", self::OneBillion, self::OneBillion, $startInt));
		}

		if(abs($endInt) > self::OneBillion || abs($endInt) > self::OneBillion){
			throw new OutOfRangeException(sprintf("We can only process numbers between %s and %s! %s provided!", self::OneBillion, self::OneBillion, $startInt));
		}

		$this->startInt = $startInt;
		$this->endInt = $endInt;

		//split it into 2 arrays, one for odd numbers and one for even numbers to make it easier to perform each task
		$this->splitNumbers($arrayOfIntegers);
	}

	public function processOddNumbers()
	{
		foreach($this->oddNumbers as $oddNumber){
			echo $this->convertIntToWord($oddNumber).PHP_EOL;
		}
	}

	public function processEvenNumbers()
	{
		$commonFactors = $this->calcFactors($this->getClosestEvenToZero($this->startInt, $this->endInt, $this->evenNumbers));

		foreach($this->evenNumbers as $evenNumber){
				$commonFactors = $this->checkIfHasCommonFactors($evenNumber, $commonFactors);
		}

		return $commonFactors;
	}

	protected function splitNumbers(array $arrayOfIntegers){
		foreach($arrayOfIntegers as $int){
			if($int & 1){
				//odd number
			 	$this->oddNumbers[] = $int;
			}else{
				//even number
				$this->evenNumbers[] = $int;
			}
		}
		sort($this->evenNumbers);//sort the even numbers as it helps calculate the closest number to 0
	}

	protected function getClosestEvenToZero($startInt, $endInt, $evenNumbers){
		$dist = abs($evenNumbers[0]);
		$idx = 0;

		$totalNo = count($evenNumbers);
		for($i=0;$i<$totalNo;$i++){
				$newDist = abs($evenNumbers[$i]);
				if($newDist < $dist){
					$idx = $i;
					$dist = $newDist;
				}
				if($dist == 0){//only if evenNumbers is sorted
					return $evenNumbers[$idx];
				}
		}

		return $evenNumbers[$idx];
	}

	protected function checkIfHasCommonFactors($int, array $minCommonFactors){
		$int = abs($int);
		$cF = $this->calcFactors($int);

		return array_intersect($cF, $minCommonFactors);
	}

	private function calcFactors($int)
	{
		$int = abs($int);
		$cF = [];
		$max = $int/2;
		for($i=1;$i<=$max;$i++){
			if(0 === $int%$i){
				$cF[] = $i;
			}
		}
		$cF[] = $int;

		return $cF;
	}

	protected function convertIntToWord($int)
	{
		$word = '';
		if($int < 0){
			$word .= 'minus ' . $this->convertIntToWord(abs($int));
		}

		if(0 <= $int && $int < 20){
			return self::$dictionary[$int];
		}

		if(20 <= $int && $int < self::OneHundred){
			return $this->processTens($int);
		}

		if (self::OneHundred <= $int && $int < self::OneThousand)
        {
        	return $this->processHundreds($int);
    	}

    	if(self::OneThousand <= $int && $int < self::OneMillion){
    		return $this->processBigNumber($int, self::OneThousand);
    	}

    	if(self::OneMillion <= $int && $int < self::OneBillion){
    		return $this->processBigNumber($int, self::OneMillion);
    	}else{
    		return $this->processBigNumber($int, self::OneBillion);
    	}
	}

	protected function processTens($int)
	{
		$tens = intval($int/10)*10;
		$units = $int%10;
		$conv = self::$dictionary[$tens];
		$conv .= $units > 0 ? '-'.self::$dictionary[$units] : '';

		return $conv;
	}

	protected function processHundreds($int)
	{
		$hundreds = intval($int/100);
		$remainder = $int%100;
		$conv = self::$dictionary[$hundreds] . ' ' . self::$dictionary[self::OneHundred];
		$conv .= $remainder > 0 ? " and " . $this->convertIntToWord($remainder) : '';

		return $conv;
	}

	protected function processBigNumber($int, $baseUnit)
	{
		$nrBaseUnits = intval($int/$baseUnit);
		$remainder = $int%$baseUnit;
		$conv = $this->convertIntToWord($nrBaseUnits) . ' ' . self::$dictionary[$baseUnit];
		$conv .= $remainder <= 0 ? "" : ($remainder < 100 ? " and " : ", ") . $this->convertIntToWord($remainder);

		return $conv;
	}
}