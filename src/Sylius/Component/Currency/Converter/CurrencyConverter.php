<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Converter;

use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CurrencyConverter implements CurrencyConverterInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $currencyRepository;

    /**
     * @var array
     */
    private $cache;

    /**
     * @param RepositoryInterface $currencyRepository
     */
    public function __construct(RepositoryInterface $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function convert($value, $code)
    {
        $currency = $this->getCurrency($code);

        if (null === $currency) {
            throw new UnavailableCurrencyException($code);
        }

        return (int) round($value * $currency->getExchangeRate());
    }

    /**
     * @param string $code
     *
     * @return CurrencyInterface
     */
    private function getCurrency($code)
    {
        if (isset($this->cache[$code])) {
            return $this->cache[$code];
        }

        return $this->cache[$code] = $this->currencyRepository->findOneBy(array('code' => $code));
    }
}
