<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Jewelry;
use App\Form\JewelryVariantFormType;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class JewelryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Jewelry::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // ->setEntityPermission('ROLE_ADMIN')
            ->setPageTitle('index', 'Bijoux :')
            ->setPageTitle('new', 'Créer un bijou')
            ->setPageTitle('edit', fn(Jewelry $jewelry) => (string) $jewelry->getName())
            ->setPageTitle('detail', fn(Jewelry $jewelry) => (string) $jewelry->getName())
            ->setEntityLabelInSingular('un bijou')
            ->setDefaultSort(['id' => 'ASC'])
            ->setPaginatorPageSize(10);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id')->hideOnForm();

        $name = TextField::new('name', 'Nom du bijou')
            ->setHelp('Un bijou ne doit être créé qu\'une seule fois. Utilisez ensuite les variantes pour gérer argent, or, etc.');

        $category = AssociationField::new('category', 'Catégorie du bijou')
            ->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => $queryBuilder
                    ->getEntityManager()
                    ->getRepository(Category::class)
                    ->createQueryBuilder('c')
                    ->orderBy('c.name', 'ASC')
            )
            ->autocomplete();

        $variantsSummary = TextField::new('variantsSummary', 'Détails du bijou')
            ->onlyOnIndex()
            ->renderAsHtml();

        $variantsDetail = TextField::new('variantsSummary', 'Détails du bijou')
            ->onlyOnDetail()
            ->renderAsHtml();

        $variants = CollectionField::new('variants', 'Détails du bijou')
            ->onlyOnForms()
            ->setEntryType(JewelryVariantFormType::class)
            ->allowAdd()
            ->allowDelete()
            ->renderExpanded()
            ->setHelp('Ajoutez ici les déclinaisons du même bijou : argent, or, etc.');

        if (Crud::PAGE_INDEX === $pageName) {
            return [
                $id,
                $name,
                $category,
                $variantsSummary,
            ];
        }

        if (Crud::PAGE_DETAIL === $pageName) {
            return [
                $id,
                $name,
                $category,
                $variantsDetail,
            ];
        }

        return [
            $name,
            $category,
            $variants,
        ];
    }
}
