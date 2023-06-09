<?php 

class PageModel {
    //model
    public int $id;
    public int $ordre;
    public string $nom ;
    public int $temps;
    public string $nomFichier;
    public string $html;
    public ?string $nomImage;
    public array $imageElements;
    //options -->
    public ?bool $estAffiche;
    public ?bool $weather;
    public ?bool $news;
    public ?bool $map;
    public ?bool $planning;
}

