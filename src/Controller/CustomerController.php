<?php 
namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    #[Route('/api/customers', name: 'create_customer', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em)
    {
        
        $data = json_decode($request->getContent(), true);
        $custName = explode(' ',$data['name']);
        $customer = new Customer();
        $customer->setFirstName($custName[0]);
        $customer->setLastName($custName[1]);
        $customer->setEmail($data['email']);
        $customer->setPhone($data['phone'] ?? null);
        // $customer->setAddress($data['address'] ?? null);
        $customer->setCreatedBy($this->getUser());
        $customer->setCreatedAt(new \DateTimeImmutable());

        $em->persist($customer);
        $em->flush();

        return new JsonResponse(['status' => 'Customer created'], 201);
    }
}