<?php

namespace Sastrawi\Stemmer\Visitor;

use Sastrawi\Stemmer\VisitorInterface;
use Sastrawi\Stemmer\ContextInterface;
use Sastrawi\Stemmer\Removal;

class DisambiguatePrefixRule23 implements VisitorInterface
{
    public function visit(ContextInterface $context)
    {
        $result = $this->disambiguatePrefixRule23($context->getCurrentWord());
        $lookup = $context->getDictionary()->lookup($result);

        if ($result === null) {
            return; 
        }
        
        $removedPart = preg_replace("/$result/", '', $context->getCurrentWord(), 1);
        
        $removal = new Removal(
            $this,
            $context->getCurrentWord(),
            $result,
            $removedPart
        );

        $context->addRemoval($removal);
        $context->setCurrentWord($result);
    }    

    /**
     * Disambiguate Prefix Rule 23
     * Rule 23 : perCAP -> per-CAP where C != 'r' AND P != 'er'
     */
    public function disambiguatePrefixRule23($word)
    {
        $contains = preg_match('/^per([bcdfghjklmnpqrstvwxyz])([a-z])(.*)$/', $word, $matches);

        if ($contains === 1) {
            if (preg_match('/^er(.*)$/', $matches[3]) === 1) {
                return;
            }
            
            return $matches[1] . $matches[2] . $matches[3];
        }
    }
}
