<?php
namespace NewVision\CoreBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use NewVision\CoreBundle\Form\Type\Loader\TreeTypeLoader;

class TreeType extends AbstractType
{
     /**
     * @var PropertyAccessorInterface
     */
    protected $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    public function getName()
    {
        return 'newvision_tree';
    }

    public function getParent()
    {
        return 'entity';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $levelPrefix = $options['levelPrefix'];
        if (empty($levelPrefix)) {
            return;
        }

        foreach ($view->vars['choices'] as $k => $choice) {

            $dataObject = $choice->data;
            $level = $this->propertyAccessor->getValue($dataObject, $options['treeLevelField']);
            $id = $this->propertyAccessor->getValue($dataObject, 'id');
            $choice->label = str_repeat($levelPrefix, $level) . '└ ' . $choice->label;

            $isLeaf = count($dataObject->getChildren()) == 0;
            if(in_array($id, $options['disabled_ids']) || in_array($level, $options['disabled_levels'])) {
                $choice->enabled = false;
            }

            if($options['disabled_leaves'] == true) {
                $choice->enabled = !$isLeaf;
            }
            if($options['disabled_inners'] == true) {
                $choice->enabled = $isLeaf;
            }

            if($options['max_level'] !== null && $options['max_level'] < $level) {
                unset($view->vars['choices'][$k]);

            }
        }
        if($options['add_empty'] !== null) {
            $choice = new \Symfony\Component\Form\Extension\Core\View\ChoiceView(null, '', $options['add_empty']);
            array_unshift($view->vars['choices'], $choice);
        }

        if ($options['upUrl']) {
            $view->vars['attr']['upUrl'] = $options['upUrl'];
        } else {
            $view->vars['attr']['upUrl'] = false;
        }
        if ($options['downUrl']) {
            $view->vars['attr']['downUrl'] = $options['downUrl'];
        } else {
            $view->vars['attr']['downUrl'] = false;
        }

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $type = $this;
        $loader = function (Options $options) use ($type) {
            if (null !== $options['query_builder']) {
                return new TreeTypeLoader($options, $options['query_builder'], $options['em'], $options['class']);
            }
        };

        $queryBuilder = function (EntityRepository $repository, Options $options) {
            $qb = $repository->createQueryBuilder('a');

            foreach ($options['orderFields'] as $columnName) {
                $qb->addOrderBy(sprintf('a.%s', $columnName));
            }
            return $qb;
        };
        $resolver->setDefaults(array(
            'loader' => $loader,
            'query_builder' => $queryBuilder,
            'expanded' => false,
            'levelPrefix' => '&nbsp;&nbsp;&nbsp;&nbsp;',
            'orderFields' => array('treeRoot', 'treeLeft'),
            'prefixAttributeName' => 'data-level-prefix',
            'treeLevelField' => 'treeLevel',
            'add_empty' => null,
            'max_level' => null,
            'upUrl' => null,
            'downUrl' => null,
            'disabled_leaves' => false,
            'disabled_inners' => false,
            'disabled_ids' => array(),
            'disabled_levels' => array(),
        ));
        $resolver->setAllowedTypes(array(
            'orderFields' => 'array',
            'prefixAttributeName' => array('string', 'null'),
            'treeLevelField' => 'string',
        ));
    }
}
