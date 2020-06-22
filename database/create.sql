
CREATE DATABASE IF NOT EXISTS `stockmalin` DEFAULT CHARACTER SET latin1;
USE `stockmalin`;

DROP TABLE IF EXISTS `achat`;
CREATE TABLE IF NOT EXISTS `achat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cout` float NOT NULL,
  `id_vendeur` int(11) NOT NULL,
  `vendeur` varchar(255) NOT NULL,
  `id_type_majoritaire` int(11) NOT NULL,
  `type_majoritaire` varchar(255) NOT NULL,
  `id_type_secondaire` int(11) NOT NULL,
  `type_secondaire` varchar(255) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='informations de base sur les achats' AUTO_INCREMENT=0 ;

DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_article` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `id_collection` int(11) NOT NULL,
  `nom_collection_commentaire` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL,
  `date_dernier_vendu` date NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='ex : bd->asterix->asterix le gaulois' AUTO_INCREMENT=0 ;

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `adresse_client` text NOT NULL,
  `adresse_livraison` text NOT NULL,
  `num_fixe` varchar(255) NOT NULL,
  `num_mobile` varchar(255) NOT NULL,
  `adresse_mail` varchar(255) NOT NULL,
  `total_achats` float NOT NULL DEFAULT 0,
  `remarques` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='informations de base sur le client' AUTO_INCREMENT=0 ;

DROP TABLE IF EXISTS `collection`;
CREATE TABLE IF NOT EXISTS `collection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `id_type` int(11) NOT NULL COMMENT 'type de collection (bd, jeux, ...)',
  `type_commentaire` varchar(255) DEFAULT '' NOT NULL COMMENT 'type de collection (bd, jeux, ...)',
  `prix` float DEFAULT 0 NOT NULL,
  `ref_stockage` varchar(255) DEFAULT '' NOT NULL,
  `date_dernier_vendu` date DEFAULT CURRENT_TIMESTAMP NOT NULL,
  `date_secondaire` date DEFAULT CURRENT_TIMESTAMP NOT NULL,
  `description` text DEFAULT '' NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Collections (bd->asterix, jeux->super nintendo)' AUTO_INCREMENT=0 ;

DROP TABLE IF EXISTS `debug`;
CREATE TABLE IF NOT EXISTS `debug` (
  `last_edit` int(11) NOT NULL,
  `explication` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `fournisseur`;
CREATE TABLE IF NOT EXISTS `fournisseur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `adresse` text NOT NULL,
  `num_tel_1` varchar(255) NOT NULL,
  `num_tel_2` varchar(255) NOT NULL,
  `adresse_mail` varchar(255) NOT NULL,
  `remarque` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

DROP TABLE IF EXISTS `lien_achat`;
CREATE TABLE IF NOT EXISTS `lien_achat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_article` int(11) NOT NULL,
  `article_commentaire` varchar(255) NOT NULL,
  `id_collection` int(11) NOT NULL,
  `collection_commentaire` varchar(255) NOT NULL,
  `id_achat` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix_unitaire` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

DROP TABLE IF EXISTS `lien_client`;
CREATE TABLE IF NOT EXISTS `lien_client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_client` int(11) NOT NULL,
  `nom_client_commentaire` varchar(255) NOT NULL,
  `prenom_client_commentaire` varchar(255) NOT NULL,
  `id_type` int(11) NOT NULL,
  `type_commentaire` varchar(255) NOT NULL,
  `id_collection` int(11) NOT NULL,
  `collection_commentaire` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

DROP TABLE IF EXISTS `lien_vente`;
CREATE TABLE IF NOT EXISTS `lien_vente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_client` int(11) NOT NULL,
  `nom_client_commentaire` varchar(255) NOT NULL,
  `prenom_client_commentaire` varchar(255) NOT NULL,
  `id_article` int(11) NOT NULL,
  `article_commentaire` varchar(255) NOT NULL,
  `id_collection` int(11) NOT NULL,
  `collection_commentaire` varchar(255) NOT NULL,
  `id_vente` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;

DROP TABLE IF EXISTS `type_collection`;
CREATE TABLE IF NOT EXISTS `type_collection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) CHARACTER SET utf8 NOT NULL,
  `ref_bon_coin` varchar(255) NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Type de collections (bd, jeux,livres pour enfants, ...)' AUTO_INCREMENT=0 ;

DROP TABLE IF EXISTS `vente`;
CREATE TABLE IF NOT EXISTS `vente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `montant_HFP` float NOT NULL,
  `montant_FP` float NOT NULL,
  `mode_envoi` varchar(255) NOT NULL,
  `id_client` int(11) NOT NULL,
  `nom_client_commentaire` varchar(255) NOT NULL,
  `prenom_client_commentaire` varchar(255) NOT NULL,
  `etat_vente` varchar(255) NOT NULL,
  `date_fin_vente` date NOT NULL,
  `id_collection_majoritaire` int(11) NOT NULL,
  `collection_majoritaire` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='informations de base sur la vente' AUTO_INCREMENT=0 ;
