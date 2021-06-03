<?php

declare(strict_types=1);

namespace App\Application\Actions\v1;

use App\Application\Actions\Action;
use pocketmine\nbt\LittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use Psr\Http\Message\ResponseInterface as Response;

class NBTAction extends Action {


    protected function action(): Response
    {
        try {
            if(!isset($this->request->getQueryParams()["nbt"])){
                return $this->respondWithData(null, 400);
            }

            $nbt = $this->request->getQueryParams()["nbt"];

            $test = (new LittleEndianNBTStream())->read(base64_decode($nbt));
            $array = ["lore" => [], "name" => ""];

            if($test instanceof CompoundTag) {
                foreach ($test->getCompoundTag("display")->getListTag("Lore") as $string){
                    $array["lore"][] = $string->getValue();
                }

                $array["name"] = $test->getCompoundTag("display")->getString("Name");
            }


            return $this->respondWithData(json_encode($array));
        } catch (\Exception $e){
            return $this->respondWithData("Error while parsing lore & custom name", 400);
        }
    }
}