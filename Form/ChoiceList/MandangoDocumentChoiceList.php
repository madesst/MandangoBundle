<?php

/*
 * This file is part of Mandango.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Mandango\MandangoBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Mandango\Query;
use Mandango\Mandango;

/**
 * MandangoDocumentChoiceList.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoDocumentChoiceList extends SimpleChoiceList
{
    private $mandango;
    private $class;
    private $field;
    private $query;

    private $documents;

    public function __construct(Mandango $mandango, $class, $field = null, Query $query = null, array $choices = array())
    {
        $this->mandango = $mandango;
        $this->class = $class;
        $this->field = $field;
        $this->query = $query;

        parent::__construct($choices);
	    $this->load();
    }

    public function getDocuments()
    {
        if (null === $this->documents) {
            $this->load();
        }

        return $this->documents;
    }

    protected function load()
    {
        //parent::load();

        if ($this->getChoices()) {
            $documents = $this->getChoices();
        } elseif ($this->query) {
            $documents = $this->query->all();
        } else {
            $documents = $this->mandango->getRepository($this->class)->createQuery()->all();
        }
        $this->documents = $documents;

        $choices = array();
	    $labels = array();
        foreach ($documents as $document) {
            if (null !== $this->field) {
                $value = $this->field;
            } elseif (method_exists($document, '__toString')) {
                $value = $document->__toString();
            } else {
                $value = $document->getId();
            }

	        $array = array();
	        $labels[(string) $document->getId()] = (string) $document->getId();
	        $choices[(string) $document->getId()] = $value;
	        $this->addChoice($array, $array, $value, (string) $document->getId(), array());
        }
    }
}
