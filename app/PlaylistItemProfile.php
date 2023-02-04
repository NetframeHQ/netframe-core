<?php

namespace App;

class PlaylistItemProfile
{
    private $item;
    private $id;
    private $type;
    private $name;
    private $model;
    private $url;

    public function __construct(PlaylistItem $item, $id, $type)
    {
        $this->item = $item;
        $this->id = $id;
        $this->type = $type;

        $this->initialize();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the profile model.
     *
     * @return object
     */
    public function getModel()
    {
        return $this->model;
    }

    public function getProfileImage()
    {
        return $this->model->profileImage;
    }

    public function getUrl()
    {
        return $this->url;
    }

    private function initialize()
    {
        switch ($this->type) {
            case \Profile::TYPE_HOUSE:
                $this->model =  App\House::findOrFail($this->id);
                $this->name = $this->model->name;
                $this->url = url()->route('page.house', array(
                    'id' => $this->model->id,
                    'slug' => $this->model->slug
                ));
                break;

            case \Profile::TYPE_COMMUNITY:
                $this->model = App\Community::findOrFail($this->id);
                $this->name = $this->model->name;
                $this->url = url()->route('page.community', array(
                    'id' => $this->model->id,
                    'name' => str_slug($this->model->name)
                ));
                break;

            case \Profile::TYPE_PROJECT:
                $this->model = App\Project::findOrFail($this->id);
                $this->name = $this->model->title;
                $this->url = url()->route('page.project', array(
                    'id' => $this->model->id,
                    'name' => str_slug($this->model->title),
                ));
                break;

            default:
                throw new \RuntimeException(sprintf('Invalid profile type "%s"', $this->type));
        }
    }
}
