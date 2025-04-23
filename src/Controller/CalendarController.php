<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Customer;
use App\Repository\EventRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/calendar')]
#[IsGranted('ROLE_USER')]
class CalendarController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private EventRepository $eventRepository;
    private CustomerRepository $customerRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        EventRepository $eventRepository,
        CustomerRepository $customerRepository
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->eventRepository = $eventRepository;
        $this->customerRepository = $customerRepository;
    }

    #[Route('/listevents', name: 'calendar_event_list', methods: ['GET'])]
    public function listEvents(EventRepository $eventRepository): JsonResponse
    {
        $user = $this->getUser();
        $events = $eventRepository->findBy(['createdBy' => $user]);
        $data = array_map(fn($event) => $event->toCalendarArray(), $events);
        return $this->json($data);
    }
    // #[Route('/events', name: 'calendar_events_list', methods: ['GET'])]
    // public function listEvents(Request $request): JsonResponse
    // {
    //     $startDate = $request->query->get('start');
    //     $endDate = $request->query->get('end');
        
    //     $events = [];
        
    //     if ($startDate && $endDate) {
    //         $startDateTime = new \DateTime($startDate);
    //         $endDateTime = new \DateTime($endDate);
    //         $events = $this->eventRepository->findByDateRange($startDateTime, $endDateTime);
    //     } else {
    //         $events = $this->eventRepository->findAll();
    //     }
        
    //     $calendarEvents = [];
    //     foreach ($events as $event) {
    //         $calendarEvents[] = $event->toCalendarArray();
    //     }
        
    //     return $this->json($calendarEvents);
    // }

    #[Route('/events/{id}', name: 'calendar_event_show', methods: ['GET'])]
    public function showEvent(int $id): JsonResponse
    {
        $event = $this->eventRepository->find($id);
        
        if (!$event) {
            return $this->json(['error' => 'Event not found'], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json($event->toCalendarArray());
    }

    #[Route('/events', name: 'calendar_event_create', methods: ['POST'])]
    public function createEvent(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['title']) || !isset($data['startDateTime']) || !isset($data['customerId'])) {
            return $this->json(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }
        
        $customer = $this->customerRepository->find($data['customerId']);
        if (!$customer) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_BAD_REQUEST);
        }
        
        $event = new Event();
        $event->setTitle($data['title']);
       
        $event->setStartDateTime(new \DateTime($data['startDateTime']));
        // $event->setStartDateTime(new \DateTime($data['startDateTime'], new \DateTimeZone('America/New_York')));
        $event->setCustomer($customer);
        $event->setCreatedBy($this->getUser());
        
        // Optional fields
        if (isset($data['endDateTime'])) {
            $event->setEndDateTime(new \DateTime($data['endDateTime']));
           }
        
        if (isset($data['description'])) {
            $event->setDescription($data['description']);
        }
        
        if (isset($data['color'])) {
            $event->setColor($data['color']);
        }
        
        if (isset($data['location'])) {
            $event->setLocation($data['location']);
        }
        
        if (isset($data['isAllDay'])) {
            $event->setIsAllDay($data['isAllDay']);
        }
        
        if (isset($data['status'])) {
            $event->setStatus($data['status']);
        }
        
        $this->entityManager->persist($event);
        $this->entityManager->flush();
        
        return $this->json(
            $event->toCalendarArray(),
            Response::HTTP_CREATED
        );
    }

    #[Route('/events/{id}', name: 'calendar_event_update', methods: ['PUT', 'PATCH'])]
    public function updateEvent(Request $request, int $id): JsonResponse
    {
        $event = $this->eventRepository->find($id);
        
        if (!$event) {
            return $this->json(['error' => 'Event not found'], Response::HTTP_NOT_FOUND);
        }
        
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['title'])) {
            $event->setTitle($data['title']);
        }
        
        if (isset($data['startDateTime'])) {
            $event->setStartDateTime(new \DateTime($data['startDateTime']));
        }
        
        if (isset($data['endDateTime'])) {
            $event->setEndDateTime(new \DateTime($data['endDateTime']));
        } elseif (array_key_exists('endDateTime', $data) && $data['endDateTime'] === null) {
            $event->setEndDateTime(null);
        }
        
        if (isset($data['description'])) {
            $event->setDescription($data['description']);
        }
        
        if (isset($data['color'])) {
            $event->setColor($data['color']);
        }
        
        if (isset($data['location'])) {
            $event->setLocation($data['location']);
        }
        
        if (isset($data['isAllDay'])) {
            $event->setIsAllDay($data['isAllDay']);
        }
        
        if (isset($data['status'])) {
            $event->setStatus($data['status']);
        }
        
        if (isset($data['customerId'])) {
            $customer = $this->customerRepository->find($data['customerId']);
            if (!$customer) {
                return $this->json(['error' => 'Customer not found'], Response::HTTP_BAD_REQUEST);
            }
            $event->setCustomer($customer);
        }
        
        $event->setUpdatedAt(new \DateTimeImmutable());
        
        $this->entityManager->flush();
        
        return $this->json($event->toCalendarArray());
    }

    #[Route('/events/{id}', name: 'calendar_event_delete', methods: ['DELETE'])]
    public function deleteEvent(int $id): JsonResponse
    {
        $event = $this->eventRepository->find($id);
        
        if (!$event) {
            return $this->json(['error' => 'Event not found'], Response::HTTP_NOT_FOUND);
        }
        
        $this->entityManager->remove($event);
        $this->entityManager->flush();
        
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    // Customer-related endpoints
    #[Route('/customers', name: 'calendar_customers_list', methods: ['GET'])]
    public function listCustomers(): JsonResponse
    {
        $customers = $this->customerRepository->findBy([], ['lastName' => 'ASC', 'firstName' => 'ASC']);
        
        $result = [];
        foreach ($customers as $customer) {
            $result[] = [
                'id' => $customer->getId(),
                'firstName' => $customer->getFirstName(),
                'lastName' => $customer->getLastName(),
                'fullName' => $customer->getFullName(),
                'email' => $customer->getEmail(),
                'phone' => $customer->getPhone()
            ];
        }
        
        return $this->json($result);
    }

    #[Route('/customers/{id}', name: 'calendar_customer_show', methods: ['GET'])]
    public function showCustomer(int $id): JsonResponse
    {
        $customer = $this->customerRepository->find($id);
        
        if (!$customer) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json([
            'id' => $customer->getId(),
            'firstName' => $customer->getFirstName(),
            'lastName' => $customer->getLastName(),
            'fullName' => $customer->getFullName(),
            'email' => $customer->getEmail(),
            'phone' => $customer->getPhone(),
            'notes' => $customer->getNotes(),
            'createdAt' => $customer->getCreatedAt()->format('c')
        ]);
    }

    #[Route('/customers', name: 'calendar_customer_create', methods: ['POST'])]
    public function createCustomer(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['firstName']) || !isset($data['lastName']) || !isset($data['email'])) {
            return $this->json(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }
        
        // Check for duplicate email
        $existingCustomer = $this->customerRepository->findOneBy(['email' => $data['email']]);
        if ($existingCustomer) {
            return $this->json(['error' => 'Email already in use'], Response::HTTP_CONFLICT);
        }
        
        $customer = new Customer();
        $customer->setFirstName($data['firstName']);
        $customer->setLastName($data['lastName']);
        $customer->setEmail($data['email']);
        $customer->setCreatedBy($this->getUser());
        
        if (isset($data['phone'])) {
            $customer->setPhone($data['phone']);
        }
        
        if (isset($data['notes'])) {
            $customer->setNotes($data['notes']);
        }
        
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
        
        return $this->json([
            'id' => $customer->getId(),
            'firstName' => $customer->getFirstName(),
            'lastName' => $customer->getLastName(),
            'fullName' => $customer->getFullName(),
            'email' => $customer->getEmail(),
            'phone' => $customer->getPhone()
        ], Response::HTTP_CREATED);
    }

    #[Route('/customers/{id}', name: 'calendar_customer_update', methods: ['PUT', 'PATCH'])]
    public function updateCustomer(Request $request, int $id): JsonResponse
    {
        $customer = $this->customerRepository->find($id);
        
        if (!$customer) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }
        
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['firstName'])) {
            $customer->setFirstName($data['firstName']);
        }
        
        if (isset($data['lastName'])) {
            $customer->setLastName($data['lastName']);
        }
        
        if (isset($data['email']) && $data['email'] !== $customer->getEmail()) {
            // Check for duplicate email only if it's changed
            $existingCustomer = $this->customerRepository->findOneBy(['email' => $data['email']]);
            if ($existingCustomer && $existingCustomer->getId() !== $customer->getId()) {
                return $this->json(['error' => 'Email already in use'], Response::HTTP_CONFLICT);
            }
            $customer->setEmail($data['email']);
        }
        
        if (isset($data['phone'])) {
            $customer->setPhone($data['phone']);
        }
        
        if (isset($data['notes'])) {
            $customer->setNotes($data['notes']);
        }
        
        $this->entityManager->flush();
        
        return $this->json([
            'id' => $customer->getId(),
            'firstName' => $customer->getFirstName(),
            'lastName' => $customer->getLastName(),
            'fullName' => $customer->getFullName(),
            'email' => $customer->getEmail(),
            'phone' => $customer->getPhone(),
            'notes' => $customer->getNotes()
        ]);
    }

    #[Route('/customers/{id}', name: 'calendar_customer_delete', methods: ['DELETE'])]
    public function deleteCustomer(int $id): JsonResponse
    {
        $customer = $this->customerRepository->find($id);
        
        if (!$customer) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }
        
        // Check if customer has events
        if (!$customer->getEvents()->isEmpty()) {
            return $this->json([
                'error' => 'Cannot delete customer with existing events. Please delete associated events first.',
                'eventCount' => $customer->getEvents()->count()
            ], Response::HTTP_CONFLICT);
        }
        
        $this->entityManager->remove($customer);
        $this->entityManager->flush();
        
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/customers/{id}/events', name: 'calendar_customer_events', methods: ['GET'])]
    public function getCustomerEvents(int $id): JsonResponse
    {
        $customer = $this->customerRepository->find($id);
        
        if (!$customer) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }
        
        $events = $this->eventRepository->findBy(['customer' => $customer], ['startDateTime' => 'DESC']);
        
        $calendarEvents = [];
        foreach ($events as $event) {
            $calendarEvents[] = $event->toCalendarArray();
        }
        
        return $this->json($calendarEvents);
    }
}