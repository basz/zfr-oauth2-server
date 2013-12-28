<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace ZfrOAuth2\Server\Service;

use DateTime;
use ZfrOAuth2\Server\Entity\AuthorizationCode;
use ZfrOAuth2\Server\Entity\Client;
use ZfrOAuth2\Server\Entity\TokenOwnerInterface;

/**
 * Service that allows to perform various operations on authorization codes
 *
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @licence MIT
 */
class AuthorizationCodeService extends AbstractTokenService
{
    /**
     * Token TTL (in seconds) for the access tokens
     *
     * The spec recommends 10 minutes: http://tools.ietf.org/html/rfc6749#section-4.1.2
     *
     * @var int
     */
    protected $tokenTTL = 600;

    /**
     * Create a new authorization token
     *
     * @param  Client              $client
     * @param  TokenOwnerInterface $owner
     * @param  string              $scope
     * @return AuthorizationCode
     */
    public function createToken(Client $client, TokenOwnerInterface $owner, $scope = '')
    {
        $expiresAt = new DateTime();
        $expiresAt->setTimestamp(time() + $this->tokenTTL);

        $authorizationCode = new AuthorizationCode();
        $authorizationCode->setToken($this->generateKey());
        $authorizationCode->setClient($client);
        $authorizationCode->setOwner($owner);
        $authorizationCode->setExpiresAt($expiresAt);
        $authorizationCode->setScope($scope);
        $authorizationCode->setRedirectUri($client->getRedirectUri());

        // Persist the access token
        $this->objectManager->persist($authorizationCode);
        $this->objectManager->flush();

        return $authorizationCode;
    }
}