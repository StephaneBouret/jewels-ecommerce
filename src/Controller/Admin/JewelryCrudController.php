<?php

namespace App\Controller\Admin;

use App\Entity\Jewelry;
use App\Entity\Category;
use Doctrine\ORM\QueryBuilder;
use App\Form\JewelryVariantFormType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

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
        // --- Champs communs
        $id = IdField::new('id')->hideOnForm();
        $name = TextField::new('name', 'Nom du bijou');
        $slug = TextField::new('slug', 'Slug');
        $category = AssociationField::new('category', 'Catégorie du bijou')
            ->setQueryBuilder(
                fn(QueryBuilder $queryBuilder) => $queryBuilder->getEntityManager()->getRepository(Category::class)->createQueryBuilder('c')->orderBy('c.name')
            )
            ->autocomplete();

        // --- Résumé HTML des variants (index uniquement)
        $variantsSummary = TextField::new('variantsSummary', 'Détails du bijou')
            ->onlyOnIndex()
            ->renderAsHtml();

        // --- Variants (formulaire new/edit uniquement)
        $variants = CollectionField::new('variants', 'Détails du bijou')
            ->onlyOnForms()
            ->setEntryType(JewelryVariantFormType::class)
            ->allowAdd()
            ->allowDelete()
            ->renderExpanded();

        if (Crud::PAGE_INDEX === $pageName) {
            return [
                $id,
                $name,
                $category,
                $slug,
                $variantsSummary,
            ];
        }

        if (Crud::PAGE_DETAIL === $pageName) {
            return [
                $id,
                $name,
                $category,
                $slug,
                TextField::new('variantsSummary', 'Détails du bijou')->renderAsHtml(),
            ];
        }

        // NEW/EDIT
        return [
            $name,
            $category,
            $slug,
            $variants,
        ];
    }
}
