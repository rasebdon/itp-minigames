<?php

class TicketComponent
{
    /** @var TicketComponent */
    public static $instance;

    function __construct()
    {
        if (isset($_GET['action']) && $_GET["action"] == "ticket") {
            $tickets = TicketService::$instance->getAllTickets();
            if(isset($_GET['deleteTicket'])){
                TicketService::$instance->deleteTicket($_GET['deleteTicket']);
            }
            $this->displayTickets($tickets);
        }
    }

    function displayTickets($tickets)
    {


?>      
        <div class="container support-ticket-header">
            <div class="row support-ticket-list">

                <div class="heading-primary">
                    <h1 class="heading-primary__text">Support Ticket System</h1>
                </div>
                <div class="user-box col-12 row support-ticket-list-item">
                    <div class="col-2 fw-bold">Ticket ID</div>
                    <div class="col-2 fw-bold">Username</div>
                    <div class="col-2 fw-bold">Subject</div>
                    <div class="col-2 fw-bold">Delete</div>
                    <div class="col-2 fw-bold">View</div>
                </div>
            </div>
        </div>
            <?php
            foreach ($tickets as $ticket)
                echo "  <div class='container support-ticket-row'>
                            <div class='user-box col-12 row support-ticket-list-item m-1'>
                                <div class='col-2 '>" . $ticket->getTicketID() . "</div>
                                <div class='col-2 '>" . $ticket->getUser()->getUsername() . "</div>
                                <div class='col-2 '>" . $ticket->getTitle() . "</div>
                                <div class='col-2 '><a class='button button--primary' href='?action=ticket&deleteTicket=".$ticket->getTicketID()."'>Delete Ticket</a></div>
                                <div class='col-2 '>
                                    <button class='button button--primary' data-bs-toggle='collapse' data-bs-target='#SupportTicketText-".$ticket->getTicketID()."'>View Ticket</button>
                                </div>
                            </div>
                            <div class='col-12 row support-ticket-list-text text-box collapse ' id='SupportTicketText-".$ticket->getTicketID()."'>
                                <p class=' text-box--1 support-ticket-text'>
                                    ".$ticket->getText()."
                                </p>
                                <div class='col-12 center'>
                                <a class='button button--primary support-ticket-mailto' href='mailto:".$ticket->getEmail()."'>Answer</a>
                                </div>
                            </div>
                        </div>";
                        
    }
}

TicketComponent::$instance = new TicketComponent;



?>

