<?php
namespace AppBundle\Controller;

use AppBundle\Util\Interfaces\PortfolioInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class SummaryController
 */
class SummaryController extends Controller
{
    /**
     * @var PortfolioInterface
     */
    private $portfolioService;

    /**
     * SummaryController constructor.
     * @param PortfolioInterface $portfolioService
     */
    public function __construct(PortfolioInterface $portfolioService)
    {
        $this->portfolioService = $portfolioService;
    }

    /**
     * Shows the summary and updates it if needed
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $portfolio = $em->getRepository('AppBundle:Portfolio')->findAll();

        //Normally we would do this on a different moment to avoid
        //keeping the user waiting
        $this->portfolioService->updatePortfolioValue($portfolio);

        return $this->render(
            'summary/index.html.twig',
            [
                'portfolio' => $portfolio,
            ]
        );
    }
}
