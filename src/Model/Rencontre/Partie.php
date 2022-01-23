<?php
/**
 * Created by Antoine Lamirault.
 */

namespace FFTTApi\Model\Rencontre;


class Partie
{
    private $adversaireA;
    private $adversaireB;
    private $scoreA;
    private $scoreB;
    private $detail;

    public function __construct(string $adversaireA, string $adversaireB, int $scoreA, int $scoreB, string $detail)
    {
        $this->adversaireA = $adversaireA;
        $this->adversaireB = $adversaireB;
        $this->scoreA = $scoreA;
        $this->scoreB = $scoreB;
        $this->detail = $detail;
    }

    public function getAdversaireA(): string
    {
        return $this->adversaireA;
    }

    public function getAdversaireB(): string
    {
        return $this->adversaireB;
    }

    public function getScoreA(): int
    {
        return $this->scoreA;
    }

    public function getScoreB(): int
    {
        return $this->scoreB;
    }

    public function getDetail()
    {
        return $this->detail;
    }

}