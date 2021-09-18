<?php
/**
 * Created by Antoine Lamirault.
 */

namespace FFTTApi\Service;


use FFTTApi\Model\Club;

class ClubFactory
{
    /**
     * @param array $data
     * @return Club[]
     */
    public function createFromArray(array $data) : array {
        $result = [];
        foreach ($data as $clubData){
            if ($clubData['validation']!=null){
            $result[] = new Club(
                $clubData['numero'],
                $clubData['nom'],
                $clubData['validation']
                );
            }
        }
        return $result;
    }
}