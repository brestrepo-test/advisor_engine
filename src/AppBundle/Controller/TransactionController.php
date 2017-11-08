<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Transaction;
use AppBundle\Util\Interfaces\PortfolioInterface;
use AppBundle\Util\Interfaces\StockPriceServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Transaction controller.
 *
 */
class TransactionController extends Controller
{
    /**
     * @var StockPriceServiceInterface
     */
    private $stockPriceService;

    /**
     * @var PortfolioInterface
     */
    private $portfolioService;

    /**
     * TransactionController constructor.
     *
     * @param StockPriceServiceInterface $stockPriceService
     * @param PortfolioInterface $portfolioService
     */
    public function __construct(StockPriceServiceInterface $stockPriceService, PortfolioInterface $portfolioService)
    {
        $this->stockPriceService = $stockPriceService;
        $this->portfolioService = $portfolioService;
    }

    /**
     * Lists all transaction entities.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $transactions = $em->getRepository('AppBundle:Transaction')->findAll();

        return $this->render('transaction/index.html.twig', array(
            'transactions' => $transactions,
        ));
    }

    /**
     * Creates a new transaction entity.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $transaction = new Transaction();
        $form = $this->createForm('AppBundle\Form\TransactionType', $transaction);
        $form->handleRequest($request);
        $parameters = [
            'transaction' => $transaction,
            'form' => $form->createView(),
            'errors' => [],
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            list($stock, $price) = $this->stockPriceService->getStockInformation($form->get('stock')->getData(), $transaction->getDate());

            if (is_null($stock)) {
                $parameters['errors'][] = "We couldn't add your transaction. Check the symbol name or try again later.";
            } elseif (is_null($price)) {
                $parameters['errors'][] = "We couldn't add your transaction. Check the the date (normally M-F) or try again later.";
            } else {
                $transaction->setSymbol($stock);
                $transaction->setUnitPrice($price);

                $em = $this->getDoctrine()->getManager();
                $em->persist($transaction);
                $em->flush();

                $this->portfolioService->updatePortfolio($stock);

                return $this->redirectToRoute('transaction_show', array('id' => $transaction->getId()));
            }
        }

        return $this->render('transaction/new.html.twig', $parameters);
    }

    /**
     * Finds and displays a transaction entity.
     *
     * @param Transaction $transaction
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Transaction $transaction)
    {
        return $this->render('transaction/show.html.twig', array(
            'transaction' => $transaction,
        ));
    }

    /**
     * Displays a form to edit an existing transaction entity.
     *
     * @param Request $request
     * @param Transaction $transaction
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Transaction $transaction)
    {
        $editForm = $this->createForm('AppBundle\Form\TransactionType', $transaction);
        $editForm->handleRequest($request);
        $parameters = [
            'transaction' => $transaction,
            'edit_form' => $editForm->createView(),
            'errors' => [],
        ];

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            list($stock, $price) = $this->stockPriceService->getStockInformation($editForm->get('stock')->getData(), $transaction->getDate());

            if (is_null($stock)) {
                $parameters['errors'][] = "We couldn't add your transaction. Check the symbol name or try again later.";
            } elseif (is_null($price)) {
                $parameters['errors'][] = "We couldn't add your transaction. Check the the date (normally M-F) or try again later.";
            } else {
                $transaction->setSymbol($stock);
                $transaction->setUnitPrice($price);

                $this->getDoctrine()->getManager()->flush();

                $this->portfolioService->updatePortfolio($stock);

                return $this->redirectToRoute('transaction_edit', array('id' => $transaction->getId()));
            }
        }

        return $this->render('transaction/edit.html.twig', $parameters);
    }

    /**
     * Deletes a transaction entity.
     *
     * @param Request $request
     * @param integer $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $transaction = $em->getRepository('AppBundle:Transaction')->find($id);
        if (!empty($transaction)) {
            $stock = $transaction->getSymbol();
            $em->remove($transaction);
            $em->flush();

            $this->portfolioService->updatePortfolio($stock);
        }

        return $this->redirectToRoute('transaction_index');
    }
}
