<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 12/6/2016
 * Time: 3:51 PM
 */

namespace Mms\Organizations\PrezentGrid\Type;


use App\Models\Organization;
use Mms\Organizations\OrganizationManager;
use Mms\Organizations\Tree\Node;
use Mms\Organizations\Tree\ToArrayVisitor;
use Prezent\Grid\BaseElementType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Prezent\Grid\ElementView;

class OrganizationRelationshipType extends BaseElementType
{
    /**
     * @var OrganizationManager
     */
    private $organizationManager;

    private $visitor;
    public function __construct(OrganizationManager $organizationManager, ToArrayVisitor $visitor)
    {
        $this->organizationManager = $organizationManager;
        $this->visitor             = $visitor;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'manager' => $this->organizationManager,
                'visitor' => $this->visitor
            ])
            ->setAllowedTypes('manager', [OrganizationManager::class])
            ->setAllowedTypes('visitor', [ToArrayVisitor::class])
        ;

    }
    /**
     * {@inheritDoc}
     */
    public function buildView(ElementView $view, array $options)
    {
        //Prepare parameters valid for all fields
        $view->vars['manager'] = $options['manager'];
        $view->vars['visitor'] = $options['visitor'];
    }

    /**
     * {@inheritDoc}
     */
    public function bindView(ElementView $view, $item)
    {
        /**
         * @var OrganizationManager $manager
         */
        $manager = $view->vars['manager'];
        /**
         * @var Organization $item
         */
        $branches = [];
        foreach ($manager->getOrganizationBranches($item) as $branch) {
            /**
             * @var Node $tree
             */
            $tree = $branch['node'];
            $data = $tree->accept($view->vars['visitor']);
            $branches[$tree->getValue()->id] = [
                'tree' => [
                    'attr' => [
                        'data-mms-handler' => 'y',
                        'data-mms-service-name' => 'tree',
                        'data-data' => json_encode([$data])
                    ]
                ],
                'path' => $branch['path']
            ];
        }
        $view->vars['value'] = $branches;
        $view->vars['branches'] = $branches;
    }

}